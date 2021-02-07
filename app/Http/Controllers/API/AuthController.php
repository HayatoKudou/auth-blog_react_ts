<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use DB;
use Mail;


class AuthController extends Controller
{
    public function login(Request $request){

        $validator = Validator::make($request->all(), [
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string'],
        ]);
        if ($validator->fails()) {
            return [
                'errors' => $validator->messages(),
            ];
        }

        $email = $request->email;
        $password = $request->password;
        $user = User::where("email",$email)->first();
        if ($user && Hash::check($password, $user->password)) {
            $token = Str::random(60);
            $user->api_token = $token;
            $user->save();
            return [
                "user" => $user,
            ];
        }else{
            return abort(401);
        }
    }

    public function register(Request $request){

        DB::beginTransaction();

        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|max:20',
                'email' => 'string|email|max:255|unique:users',
                'password' => 'string|max:255',
            ]);
            if ($validator->fails()) {
                return [
                    'errors' => $validator->messages(),
                ];
            }

            $name = $request->name;
            $email = $request->email;
            $password = $request->password;
           
            $user_model = new User;
            $user_model->fill([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make($password),
            ]);
            $user_model->save();

            DB::commit();
            return [
                "user" => $user_model,
            ];
        } catch (Exception $e) {
            DB::rollback();
            return abort(401);
        }
    }

    public function contact(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'contents' => 'required|max:255',
            ]);
            if ($validator->fails()) {
                return [
                    'errors' => $validator->messages(),
                ];
            }
            Mail::send('emails.mail', [
                'email' => $request->email,
                'contents' => $request->contents,
            ], function($message){
                $message->to('kudoh115@gmail.com')
                ->from('hayatoportfolio@gmail.com')
                ->subject('gamer-lab.netからのお問い合わせ');
            });
            return [
                'message' => 'お問い合わせを受け付けました。'
            ];
        } catch (Exception $e) {
            return abort(401);
        }
    }

}
