<?php

namespace App\Http\Controllers\AuthApi;

use App\User;
use http\Message;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller as Controller;

class ConfirmController extends Controller
{
    public function confirm($confirmation_code)
    {
        if( !$confirmation_code)
        {
            return response()->json(['error' => 'You have not verified account.']);
        }

        $user = User::where('confirmation_code' ,$confirmation_code)->first();

        if ( !$user)
        {
            return response()->json(['error' => 'You have not verified account.']);
        }

        $user->confirmed = 1;
        $user->confirmation_code = null;
        $user->update();

        return response()->json(['success' => 'you had activated this account successfully.']);
    }
}
