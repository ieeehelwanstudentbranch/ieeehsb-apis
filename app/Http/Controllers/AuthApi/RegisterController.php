<?php

namespace App\Http\Controllers\AuthApi;

use App\Ex_com_options;
use App\HighBoardOptions;
use Illuminate\Http\Request;
//use App\Http\Controllers\Controller;
use App\Http\Controllers\Controller as Controller;
use App\Http\Requests;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Facades\JWTFactory;
use JWTAuthException;
use App\User;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
protected $user;
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function register(Request $request)
    {
        $req = $request;
        $this->validate($request ,[
            'firstName' => 'required |string | max:50 | min:5',
            'lastName' => 'required |string | max:50 | min:5',
            'faculty' => 'required |string',
            'university' => 'required |string',
//            'DOB' => 'date_format:Y-M-D|before:today',
            'email' => 'required |string|email|max:255| unique:users',
            'password'=>'required|confirmed|string|min:6',
            'password_confirmation'=>'sometimes|required_with:password',
        ]);
         //if position EX-com
        if ($request->input('position')=='EX-com') {$this->validate($request, ['EX-comOptions' => 'required']);}

        //if position High board and the committee was chosen RAS, PES, WIE:
        if ($request->input('position')=='highBoard'&& ($request->input('committee')=='RAS' || 'PES' || 'WIE'))
        {$this->validate($request, ['highBoardOptions' => 'required']);}

        $confirmation_code = str_random(30);

        $user= new User();
        $user->firstName= $request->input('firstName');
        $user->lastName= $request->input('lastName');
        $user->faculty= $request->input('faculty');
        $user->university= $request->input('university');
        $user->DOB= $request->input('DOB');
        $user->position= $request->input('position');
        $user->email=$request->input('email');
        $user->confirmation_code  = $confirmation_code ;
        $user->password=app('hash')->make($request->input('password'));

        if ($request->input('position')=='EX_com'){
            $ex = new Ex_com_options();
            $ex->ex_options = $request->input('ex_options');
            if ($ex->ex_options!=null){
                $user->save();
                $ex->user_id = $user->id;
                $ex->save();
            }else{return response()->json('error');}
        }

        if ($request->input('position')=='highBoard' || 'volunteer'){
            $user->committee = $request->input('committee');
        }

        if ($request->input('position')=='highBoard' && ($request->input('committee')==('RAS'||'PES' || 'WIE') )){
            $hb = new HighBoardOptions();
            $hb->HB_options = $request->input('highBoardOptions');
            if ($hb->HB_options != null){
                $user->save();
                $hb->user_id = $user->id;
                $hb->save();
            }else{return response()->json('error');}
        }

        if ($request->input('position')!='EX_com' && ($request->input('position')=='highBoard' && ($request->input('committee')==('RAS'||'PES' || 'WIE') )))
        {
            $user->save();
        }

//        send activation email
        Mail::send('/emails.verify', compact(['user','confirmation_code']), function($message) use ($req) {
            $message->to($this->MailTarget($req), 'user')->subject('Verify your email address');
        });

           return response()->json(['status' =>'success','user'=>$user]);
        }


//        mail target
        public function MailTarget(Request $request)
        {
            $email = 'mhmdy4554@gmail.com';

//            if Ex-com register
            if ($request->input( 'position')=='EX_com' && ($request->input('ex_options')!='Chairperson') ){
                $ex = Ex_com_options::where('ex_options','Chairperson' )->first();
                $user = User::findOrFail($ex->user_id);
                $email = $user->email;
            }

////            if high board register
//            if ($request->input( 'position')=='highBoard' && ($request->input('committee')!='Chairperson') ){
//                $ex = Ex_com_options::where('ex_options','Chairperson' )->first();
//                $user = User::findOrFail($ex->user_id);
//                $email = $user->email;
//            }


            return $email;


        }


 }
        


