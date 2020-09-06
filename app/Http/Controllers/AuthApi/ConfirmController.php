<?php

namespace App\Http\Controllers\AuthApi;

use App\User;
use App\Volunteer;
use App\Status;
use http\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
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
        else{
        $user->confirmed = 1;
        $user->confirmation_code = null;
        $user->update();
        $email = $user->email;
        if($user->type == 'volunteer')
        {
          $user->volunteer->status_id = Status::where('name','activated')->value('id');
          $user->volunteer->update();

        }

        return response()->json(['success' => 'You have verified your account.Please Check Your Mail']);

            Mail::send('emails.confirm',compact(['user']), function($message) use ($email) {

            $message->to($email, 'user')->subject('Confrim Mail');

        });
      }

        }
}
