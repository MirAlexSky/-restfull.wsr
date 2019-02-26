<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use App\User;
use Validator;

class AuthController extends Controller
{
    private $apitoken;

    public function __construct() {
        //unique token
        $this->apitoken = uniqid(base64_encode(str_random(10)));
    }

    /**
     * client login
     * 
     * @param Request $request
     */
    public function postLogin(Request $request) {
        $rules = [
            'login' => 'required',
            'password' => 'required'
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'false',
                'message' => $validator->messages()
            ]);
        } else {
            // Fetch user
            $user = User::where('login', $request->login)->first();

            if ($user != null) {
                if (password_verify($request->password, $user->password)) {
                    //Update token
                    $postArray = ['api_token' => $this->apitoken];
                    $login = User::where('login', $request->login)->update($postArray);

                    if ($login) {
                        return response()->json([
                            'status' => 'true',
                            'token' => base64_encode($request->login) . ":" . $this->apitoken
                        ]);
                    }
                } else {
                    return response()->json([
                        'status' => 'false',
                        'Message' => 'Invalid auth data: password is wrong',
                        'pass_user' => $user->password,
                        'your_pass' => md5($request->password),
                    ])->setStatusCode(401, 'Invalid auth data');
                }
            } else {
                return response()->json([
                    'status' => 'false',
                    'Message' => 'Invalid auth data: login is wrong',
                ])->setStatusCode(401, 'Invalid auth data');
            }
        }
    }

    /**
     * postRegister
     * 
     * @param Request $requset
     */
    public function postRegister(Request $request) {
        
        // Validation
        $rules = [
            'login' => 'required',
            'password' => 'required'
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'false',
                'message' => $validator->messages()
            ]);
        } else {
            $user = User::insert([
                'login' => $request->login,
                'password' => password_hash($request->password, PASSWORD_DEFAULT),
                'api_token' => $this->apitoken
            ]);

            if ($user) {
                return response()->json([
                    "status" => 'newUser Successful creation'
                ]);
            } else {
                return response()->json([
                    "status" => 'register failed, pls try again'
                ]);
            }
        }
    }
    
}
