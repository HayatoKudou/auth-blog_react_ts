<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\models\Article;
use DB;

class PostController extends Controller
{
    public function post(Request $request){
        DB::beginTransaction();
        try{
            $validator = Validator::make($request->all(), [
                'content' => 'required|string|max:255',
            ]);
            if ($validator->fails()) {
                return [
                    'errors' => $validator->messages(),
                ];
            }
            $article_model = new Article;
            $article_model->fill([
                'content' => $request->content,
            ]);
            $article_model->save();
            DB::commit();

        } catch (Exception $e) {
            DB::rollback();
            return abort(401);
        }
    }
}
