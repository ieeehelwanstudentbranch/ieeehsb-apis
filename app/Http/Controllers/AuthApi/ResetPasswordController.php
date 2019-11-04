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
            return response()->json([
                'response' => 'failed',
                'error' => $error_message
            ],404);
        }
        try {
            $reset_code = str_random(30);
            $user->token = $reset_code;
            $user->update();


            Mail::send('emails.auth.reminder', compact(['reset_code','user']), function($message) use  ($request) {
                $message->to($request->email, 'user')->subject('Reset your email password');
            });

        } catch (\Exception $e) {
            $error_message = $e->getMessage();
            return response()->json(['response' => 'failed', 'error' => $error_message], 404);
        }
        return response()->json([
            'response' => 'success',
            'message'=> 'A reset email has been sent! Please check your email.'
        ]);
    }

    public function Reset(Request $request ,$reset_code)
    {
        $this->validate($request, [
            'password' => 'required|min:6|confirmed',
        ]);

        if( ! $reset_code){
            return response()->json(['response' =>'Failed','error'=>'there is no reset code provider'], 401);
        }

        $user = User::where('token' , $reset_code)->first();

        if ( ! $user)
        {
            return response()->json(['response' =>'Failed','error'=>'Sorry, you don\'t have a valid reset code.'], 404);
        }

        $user->password=app('hash')->make($request->input('password'));
        $user->token = null;
        $user->update();
        // Flash::message('You have successfully verified your account. You can now login.');
        return response()->json(['response' =>'success','message'=>'Your password was reset successfully']);
    }
}
