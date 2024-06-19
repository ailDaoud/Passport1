<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Exception;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                "email" => 'required|email|exists:users',
                "password" => "string|required|min:6"
            ]);
            if ($validation->fails()) {
                return response()->json([
                    'sucsess' => 0,
                    'result' => null,
                    'message' => $validation->errors(),
                ], 200);
            }

            $user = User::where('email', $request->email)->first();
            if(!empty($user)){
                if(Hash::check($request->password,$user->password)){
                   $token=$user->createToken('token')->accessToken;
                   return response()->json([
                    'sucsess' => 1,
                    'result' => "",
                    'message' => "user login sucsessfully",
                    'token'=>$token
                ], 200);
                }
                else{
                    return response()->json([
                        'sucsess' => 0,
                        'result' => null,
                        'message' => "password not correct",
                    ], 200);

                }
            }
            else{
                return response()->json([
                    'sucsess' => 0,
                    'result' => null,
                    'message' => "user not found",
                ], 200);
            }

        } catch (Exception $e) {
            return response()->json([
                'sucsess' => 0,
                'result' => null,
                'message' => $e,
            ], 200);
        }
    }
    public function register(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                "email" => 'required|string|email|unique:users',
                'name' => 'required|string',
                "password" => "string|required|min:6"
            ]);

            if ($validation->fails()) {
                return response()->json([
                    'sucsess' => 0,
                    'result' => null,
                    'message' => $validation->errors(),
                ], 200);
            }
            $user = User::create([
                'email' => $request->email,
                'name' => $request->name,
                //  'password' => Hash::make($request->password),
                'password' => bcrypt($request->password)
            ]);
            /* $user->email = $request->email;
            $user->name = $request->name;
            $user->password = Hash::make($request->password);
            $user->save();*/
            return response()->json([
                'sucsess' => 1,
                'result' => $user,
                'message' => 'user created sucsessfully',
               // 'token' => $user->createToken("API-TOKEN")->plainTextToken
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'sucsess' => 0,
                'result' => null,
                'message' => $e->getMessage(),
            ], 200);
        }
    }
}
