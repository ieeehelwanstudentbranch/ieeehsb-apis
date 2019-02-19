<?php

namespace App\Http\Controllers\AuthApi;

use App\User;
use Illuminate\Auth\Passwords\PasswordBroker;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;

class ResetPasswordController extends Controller
{
    public function recover(Request $request)
    {
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            $error_message = "Your email address was not found.";
            return response()->json(['success' => false, 'error' => ['email'=> $error_message]], 401);
        }
        try {
            $reset_code = str_random(30);
            $user->token = $reset_code;
            $user->update();


            Mail::send('emails.auth.reminder', compact(['reset_code','user']), function($message) use  ($request) {
                $message->to($request->email, 'user')->subject('Verify your email address');
            });

        } catch (\Exception $e) {
            $error_message = $e->getMessage();
            return response()->json(['success' => false, 'error' => $error_message], 401);
        }
        return response()->json([
            'success' => true, 'data'=> ['message'=> 'A reset email has been sent! Please check your email.']
        ]);
    }



    public function Reset(Request $request ,$reset_code)
    {
        $this->validate($request, [
            'password' => 'required|min:6|confirmed',
        ]);


        if( ! $reset_code)
        {
            return Redirect::home();
        }

        $user = User::where('token' , $reset_code)->first();

        if ( ! $user)
        {
            return Redirect::home();
        }

        $user->password=app('hash')->make($request->input('password'));
        $user->token = null;
        $user->update();

//        Flash::message('You have successfully verified your account. You can now login.');

        return response()->json(['status' =>'success','Your Password Reset']);
    }

}
