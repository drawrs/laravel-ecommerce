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
    
}
