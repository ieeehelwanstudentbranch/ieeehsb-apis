<?php

namespace App\Http\Controllers\AuthApi;

use App\Committee;
use App\Ex_com_options;
use App\HighBoardOptions;
use App\Http\Resources\Post\RegisterCollection;
use Egulias\EmailValidator\Exception\ExpectingCTEXT;
use Illuminate\Http\Request;
//use App\Http\Controllers\Controller;
use App\Http\Controllers\Controller as Controller;
use App\Http\Requests;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
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

    public function registerPage(){
        $committee = Committee::all();
        return new RegisterCollection($committee);
    }

    public function register(Request $request)
    {
        $req = $request;
        Input::merge(array_map('trim', Input::all()));
        $this->validate($request ,[
            'firstName' => 'required |string | max:50 | min:3',
            'lastName' => 'required |string | max:50 | min:3',
            'faculty' => 'nullable |string | max:30 | min:3',
            'university' => 'nullable |string | max:30 | min:3',
            'DOB' => 'nullable|date_format:m-d-Y|before:today',
            'email' => 'required |string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed|regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{6,}$/',
            'password_confirmation'=>'sometimes|required_with:password',
        ]);
         //if position EX-com
        if ($request->input('position')=='EX_com') {$this->validate($request, ['ex_options' => 'required']);}

        //if position High board and the committee was chosen RAS, PES, WIE:
        if ($request->input('position')=='highBoard' && ($request->input('committee')== 'RAS' || $request->input('committee')==  'PES' || $request->input('committee')==  'WIE'))
        {$this->validate($request, ['highBoardOptions' => 'required']);}

        $confirmation_code = str_random(30);

        $user= new User();
        $user->firstName= $request->input('firstName');
        $user->lastName= $request->input('lastName');
        $user->image= 'default.jpg';
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
            }else{
                return response()->json('error');
            }
        }

        if ($request->input('position')=='highBoard' || $request->input('position')== 'volunteer'){
//            $user->committee = $request->input('committee');
            $committee = Committee::where('name', $request->input('committee'))->first();
            $user->committee_id = $committee->id;
        }

        if (($request->input('position')=='highBoard') && ($request->input('committee')=='RAS'|| $request->input('committee')== 'PES' || $request->input('committee')=='WIE') ){
            $hb = new HighBoardOptions();
            $hb->HB_options = $request->input('highBoardOptions');
            if ($hb->HB_options != null){
                $user->save();
                $hb->user_id = $user->id;
                $hb->save();
            } else {
                return response()->json('error');
            }
        }

        if ( $request->input('position')!='EX_com' && ($request->input('position') =='highBoard' && ($request->input('committee') != 'RAS'|| $request->input('committee') != 'PES' || $request->input('committee') != 'WIE' )))
        {
            $user->save();
        }

        if ($request->input('position')=='volunteer'){$user->save();}
//        send activation email
        Mail::send('/emails.verify', compact(['user','confirmation_code']), function($message) use ($req) {
            $message->to($this->MailTarget($req), 'user')->subject('Verify your email address');
        });
        if ($user->id) {
            return response()->json(['status' => 'success', 'message' => 'Registration is Successful, please wait until your account being activated']);
        }else{
            return response()->json(['status' => 'fail', 'message' => 'Registration is Fail, please check your data again!']);

        }
    }


        //  mail target
        public function MailTarget(Request $request)
        {
            $email =  'ieeehelwanstudentbranch@gmail.com';

            // if Ex-com(Chairperson) register
            if ($request->input('position')=='EX_com' && ($request->input('ex_options')=='chairperson') ){
                $email = 'ieeehelwanstudentbranch@gmail.com';
            }

            // if Ex-com(!Chairperson) register
            if ($request->input('position')=='EX_com' && ($request->input('ex_options')!='chairperson') ) {
                try {
                    $ex = Ex_com_options::where('ex_options', 'chairperson')->first();
                    $user = User::findOrFail($ex->user_id);
                    $email = $user->email;

                } catch (JWTAuthException $e) {
                    $email = 'ieeehelwanstudentbranch@gmail.com';
                }
            }

            // if High Board register
            if ($request->input('position')=='highBoard') {
                try {
                    $committee = Committee::where('name', $request->input('committee'))->first();
                    $mentor =User::where('id', $committee->mentor_id);
                    $email = $mentor->email;

                } catch (JWTAuthException $e) {
                    $email = 'ieeehelwanstudentbranch@gmail.com';
                }
            }

            // if volunteer register
            if ($request->input('position')=='volunteer') {
                try {
                    $committee = Committee::where('name', $request->input('committee'))->first();
                    if ($committee->director_id) {
                        $director = User::where('id', $committee->director_id);
                        $email = $director->email;
                    }else {
                        $mentor = User::where('id', $committee->mentor_id);
                        $email = $mentor->email;
                    }

                } catch (JWTAuthException $e) {
                    $email = 'ieeehelwanstudentbranch@gmail.com';
                }
            }

            return $email;
        }
}



