<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Log;
use Twitter;
use App\Models\Access;
use App\Models\Article;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class TopController extends Controller
{
    public function getTwitterData(){
        $result = Twitter::get('statuses/user_timeline', array(
            "count" => 1,
            "exclude_replies" =>  True,
            "include_rts"=>  False,
        ));
        return $result;
    }

    public function getQiitaData(){
        $token = 'e5f492aedaf7d3d94d1e8088a05471c8c2504ef4';
        $client = new Client;
        $result = $client->request('GET', 'https://qiita.com/api/v2/authenticated_user/items?page=1&per_page=10', [
            'headers' => [
                'Authorization' => 'Bearer '.$token,
                'Accept' => 'application/json',
            ],
        ]);
        $response_body = $result->getBody();
        $decode_res = json_decode($response_body);
        foreach ($decode_res as $data) {            
            $article_exists_check = Article::where('url', $data->url)->exists();
            //DB登録
            if(!$article_exists_check){
                $article_model = new Article;
                $article_model->fill([
                    'type' => 'qiita',
                    'url' => $data->url,
                    'content' => $data->title,
                    'date' => date( "Y-m-d", strtotime($data->created_at)),
                ]);
                $article_model->save();
            }
            $result_data[] = [
                'date' => date( "Y-m-d", strtotime($data->created_at)),
                'notice_content' => $data->title,
                'url' => $data->url,
            ];
        }
        return $result_data;
    }

    public function topData(Request $request){
        $twitterData = $this->getTwitterData();
        $qiitaData = $this->getQiitaData();
        $article_model = Article::all();
        if($request->ip() != '219.102.198.3'){
            $check_access_model = Access::all();
            if(!empty($check_access_model)){
                $max_count = Access::max('id');
                $access_model = Access::find($max_count);
                $access_model->fill([
                    'url' => 'https://kudohayatoblog.com',
                    'count' => $access_model->count + 1,
                ]);
            } else {
                $access_model = new Access;
            }
            $access_model->save();
        }
        foreach($article_model as $a_data){
            $articleData[] = [
                'date' => date('Y-m-d', strtotime($a_data->date)),
                'content' => $a_data->content,
                'type' => $a_data->type,
                'url' => $a_data->url,
            ];
        }
        $responese = [
            'twitterData' => $twitterData,
            // 'qiitaData' => $qiitaData,
            'articleData' => $articleData,
        ];
        return $responese;
    }

    public function getAdminData(){
        $max_count = Access::max('id');
        $access_model = Access::find($max_count);
        return [
            'access' =>  $access_model->count,
        ];
    }

    public function post(Request $request){
        $article_model = new Article;
        $article_model->fill([
            'type' => $request->type,
            'url' => $request->url,
            'path' => $request->path,
            'content' => $request->contents,
        ]);
        $article_model->save();
        $message = ['message' => '投稿しました。'];
        return $message;
    }

    //記事検索
    public function article_search(Request $request){      
        
        $result = [] ;
        if(is_null($request->keyword)){
            return $result;
        }
        
        $keyword = $request->keyword;
        $article_data = Article::all();        

        foreach($article_data as $value){
            $content = $value->content;
            $url = $value->url;
            $path = $value->path;
            if(strpos($content, $keyword) !== false){
                $result[] = [
                    'content' => $content,
                    'url' => $url,
                    'path' => $path,
                ];
            }
        }
        return $result;
    }
}
