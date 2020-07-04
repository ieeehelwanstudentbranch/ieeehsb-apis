<?php

namespace App\Http\Controllers\AuthApi;

use App\User;
use App\Volunteer;
use App\Status;
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
        $vol = Volunteer::where('user_id',$user->id)->first();
        if($vol != null)
        {
          $vol->status_id = Status::where('name','activated')->value('id');
          $vol->update();
            Mail::send(['success'=>'Welcome To The Branch.You had activated your account'] function($message) {
            $message->to($user->email; 'user')->subject('Acceptance Mail');
        });
          }
          else{
                return response()->json(['success' => 'you had activated this account successfully.']);

          }

        }


    }
}
