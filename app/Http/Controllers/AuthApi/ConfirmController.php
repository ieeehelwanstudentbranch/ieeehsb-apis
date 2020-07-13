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

        $user->confirmed = 1;
        $user->confirmation_code = null;
        $user->update();
        $email = $user->email;
        $vol = Volunteer::where('user_id',$user->id)->first();
        if($vol != null)
        {
          $vol->status_id = Status::where('name','activated')->value('id');
          $vol->update();
        $data = []; // Empty array
            Mail::send('emails.confirm',compact(['user']), function($message) use ($email) {

            $message->to('zeka.bolbol@gmail.com', 'user')->subject('Confrim Mail');

        });

          }
          else{
                return response()->json(['error' => 'You have not verified account.']);

          }

        }
}
