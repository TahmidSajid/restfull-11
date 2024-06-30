<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function register_user(Request $request){

        $data = $request->all();
        $rules = [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required',
            'confirm_password' => 'required',
        ];
        $message =[
            'name.required' => 'name is required',
            'email.required' => 'email is required',
            'email.email' => 'email is not valid',
            'email.unique' => 'email is already in use',
            'password.required' => 'password is required',
            'confirm_password.required' => 'confirm password is required',
        ];

        $validation = Validator::make($data,$rules,$message);

        if ($validation->fails()) {
            return response()->json(['message' => $validation->errors()]);
        }

        if ($request->password != $request->confirm_password) {
            return response()->json(['message' => 'confirm password doesnot match']);
        }

        $user = User::create($request->except('confirm_password'));

        $token = $user->createToken('myapp')->accessToken;

        User::where('id',$user->id)->update([
            'access_token' => $token,
        ]);

        return response()->json(['access_token' => $token]);

    }
}
