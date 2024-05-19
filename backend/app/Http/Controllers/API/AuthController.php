<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request){
        // validation the requirements of request body
        $payload = $request->validate([
            "name" => "required|min:2|max:60",
            "email" => "required|email|unique:users,email",
            "password" => "required|min:6|max:50|confirmed"
        ]);

        // dd("<<<<<<<<<<");

        // Try To Create User
        try{
            $payload['password'] = Hash::make($payload['password']);
            User::create($payload);
            return response()->json(["status"=>200,"message"=>"User Created Successfully"]);
        }
        // Catch the Error if User Creation Failed
        catch(\Exception $e){
            return response([
                "message"=>$e->getMessage(),
                "status"=>500
            ]);
        }
    }

    public function login(Request $request){
        // validation the requirements of request body
        $payload = $request->validate([
            "email"=>"request|email|unique:users,email",
            "password"=>"request|min:6|max:50|confirmed"
        ]);

        // Try To Create User
        try{
            // Get the user based on email
            $user = User::where(['email'=>$payload['email']]);

            // Check if user exists
            if($user){

                // Compare the password with hashed password on database
                $compare = Hash::check($payload['password'],$user->password);

                // If password are invalid
                if(!$compare){
                    return response()->json(['status'=>401,'message'=>'Password are invalid']);
                }

                // Create token
                $token = $user->createToken('rahasia')->plainTextToken;

                // Merge the user array with token
                $authResponse = array_merge($user->toArray(),["token"=>$token]);

                return response()->json(["status"=>200,"message"=>"User Created Successfully"]);
            }

            // Return if user is not found
            return response()->json(["status"=>401,"message"=>"User Not Found"]);
        }
        // Catch the Error if User Creation Failed
        catch(\Exception $e){
            return response([
                "message"=>$e->getMessage(),
                "status"=>500
            ]);
        }

    }
}
