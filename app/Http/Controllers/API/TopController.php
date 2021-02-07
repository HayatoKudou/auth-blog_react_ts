<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Log;
use Twitter;
use App\Notice;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class TopController extends Controller
{
    public function getTwitterData(){
        $result = \Twitter::get('statuses/user_timeline', array(
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
            $result_data[] = [
                'date' => date( "Y-m-d", strtotime($data->created_at)),
                'notice_content' => $data->title,
                'url' => $data->url,
            ];
        }
        return $result_data;
    }

    public function topData(){
        $twitterData = $this->getTwitterData();
        $qiitaData = $this->getQiitaData();
        $responese = [
            'twitterData' => $twitterData,
            'qiitaData' => $qiitaData,
        ];
        return $responese;
    }
}
