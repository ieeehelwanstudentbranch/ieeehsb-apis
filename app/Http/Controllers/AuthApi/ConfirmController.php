<?php

namespace App\Http\Controllers\AuthApi;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller as Controller;

class ConfirmController extends Controller
{
    public function confirm($confirmation_code)
    {
        if( !$confirmation_code)
        {
//            throw new InvalidConfirmationCodeException;
            return redirect()->back()->with('error','You have not verified account.');
        }

        $user = User::where('confirmation_code' ,$confirmation_code)->first();

        if ( !$user)
        {
//            throw new InvalidConfirmationCodeException;
            return redirect()->back()->with('error','You have not verified account.');
        }

        $user->confirmed = 1;
        $user->confirmation_code = null;
        $user->update();

//        Flash::message('You have successfully verified your account.');

        return redirect('/https://www.google.com')->with('success','You have successfully verified account.');
    }
}
