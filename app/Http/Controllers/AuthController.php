<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Purifier;
use Hash;
use JWTAuth;
use App\User;
use Response;

class AuthController extends Controller
{
    public function SignUp(Request $request)
    {
      $rules=[
        "username" => "required",
        "email" => "required",
        "password" => "required"
      ];
      $validator = Validator::make(Purifier::clean($request->all()), $rules);

      if($validator->fails())
      {
        return Response::json(["error"=>"please fill out all of the fields"]);
      }

      $check = User::where("email","=",$request->input('email'))->orWhere("name","=",$request->input('username'))->first();
      if(!empty($check))
      {
        return Response::json(["error"=>"You've already been here."]);
      }
      
      if(strlen($request->input("username"))>12)
      {
        return Response::json(["error"=>"Your username is too long."]);
      }

      if(strlen($request->input("email"))>32)
      {
        return Response::json(["error"=>"Your email is too long. "])
      }

      if(strlen($request->input("password"=>))<8)
      {
        return Response::json(["error"=>"Your password must be at least 8 characters"])
      }

      $user = new User;
      $user->name = $request->input("username");
      $user->email = $request->input("email");
      $user->password= Hash::make($request->input("password"));
      $user->save();

      return Response::json(["success"=>"You did it!"]);
    }

    public function SignIn(Request $request)
    {
      $rules=[
        "email" => "required",
        "password" => "required"
      ];
      $validator = Validator::make(Purifier::clean($request->all()), $rules);

      if($validator->fails())
      {
        return Response::json(["error"=>"please fill out all fields"]);
      }

      $email=$request->input("email");
      $password=$request->input("password");

      $credentials=compact("email","password",["email","password"]);
      $token = JWTAuth::attempt($credentials);

      return Response::json(compact("token"));

    }
}
