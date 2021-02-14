<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ToolsController extends Controller
{
    public function get_api_endPoint(){
        return response()->json(['status' => 'success']);
    }

    public function post_api_endPoint(){
        return response()->json(['status' => 'success']);
    }
}
