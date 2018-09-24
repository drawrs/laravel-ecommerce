<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Auth;
class ApiController extends Controller
{
    //
    public function userLogin(Request $request){
        $email = $request->email;
        $password = $request->password;

        $isSuccess = false;
        $message = "Login gagal!";
        $data = null;
        $response_status = 200;

        if (Auth::attempt(['email' => $email, 'password' => $password])) {
            // Authentication passed...
            $user = User::where('email', $email)->first();
            $isSuccess = true;
            $message = "Login berhasil";
            $data = $user;
        } 
        return response()->json(
                        compact('isSuccess', 
                        'response_status', 
                        'message', 
                        'data')
                    );
    }
    public function registerUser(Request $request){
        $user = new User;

        $email = $request->email;
        $password = bcrypt($request->password);
        $first_name = $request->first_name;
        $last_name = $request->last_name;
        //'name', 'email', 'password', 'first_name', 'last_name', 'status'
        $isSuccess = false;
        $message = "Register gagal!";
        $data = null;
        $response_status = 200;

        $create = null;
        try {
            // insert info user tbl
            $create = $user->create(compact('email', 'password', 'first_name', 'last_name'));
        } catch (\Exception $email){
            $errorCode = $e->errorInfo[1];
            if($errorCode == 1062){
                $isSuccess = false;
                $message = "Email telah terdaftar";
                $data = null;
            }
        }

        if (!is_null($create)) {
            // insert seller detail
            $isSuccess = true;
            $message = "Register berhasil";
            $data = $create;
        }

        return response()->json(compact('isSuccess', 'response_status', 'message', 'data'));

    }
}
