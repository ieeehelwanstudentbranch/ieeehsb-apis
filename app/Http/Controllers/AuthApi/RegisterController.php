<?php

namespace App\Http\Controllers\AuthApi;

use App\User;
use App\Committee;
use App\Status;
use App\Position;
use App\Volunteer;
use App\Participant;
use App\Http\Controllers\Controller as Controller;
use App\Http\Resources\Post\RegisterCollection;
use Egulias\EmailValidator\Exception\ExpectingCTEXT;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Mail;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Facades\JWTFactory;
use JWTAuthException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;


class RegisterController extends Controller
{
protected $user;
    public function __construct(User $user)
    {
        // $this->user = $user;
    }

    public function registerPage(){
        $committee = Committee::all();
        return new RegisterCollection($committee);
    }

    public function register(Request $request)
    {
        $req = $request;
        $type = $request->input('type');
        Input::merge(array_map('trim', Input::all()));
        $validator = Validator::make($request->all(), [
            'firstName' => 'required |string | max:50 | min:3',
            'lastName' => 'required |string | max:50 | min:3',
            // 'faculty' => 'nullable |string | max:30 |   min:3',
            // 'university' => 'nullable |string | max:30 | min:3',
            // 'DOB' => 'nullable|date_format:Y-m-d|before:today',
            'email' => 'required |string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            // |regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{6,}$/'
            'password_confirmation'=>'sometimes|required_with:password',
            'type' =>'required|string',
        ]);

        if ($validator->fails()) {
          return response()->json(['errors'=>$validator->errors()]);
        }
         //if position EX-com
         if ($request->input('type')=='volunteer') {Validator::make($request->all(), ['role' => 'required']);}

        if ($request->input('role')=='EX_com') { Validator::make($request->all(), ['ex_options' => 'required']);}

        $confirmation_code = str_random(30);
        $user= new User();
        $user->firstName= $request->input('firstName');
        $user->lastName= $request->input('lastName');
        // $user->image= 'default.jpg';
        // $user->faculty= $request->input('faculty');
        // $user->university= $request->input('university');
        // $user->DOB= $request->input('DOB');
        $user->email=$request->input('email');
        $user->confirmation_code  = $confirmation_code ;
        $user->password=app('hash')->make($request->input('password'));

        if ($request->input('type')== 'volunteer'){
          if ($request->input('role')=='EX_com'){

            $vol = new Volunteer();
            $user->save();
            $vol->user_id = $user->id;
            $vol->status_id = Status::where('name','deactivated')->value('id');
            if($request->input('ex_options') != null)
            {
              $vol->position_id = Position::where('name',$request->input('ex_options'))->value('id');
              $vol->save();
            }
            else {
              return response()->json(['message'=>'Ex Options Required']);

            }
        }

        if ($request->input('role')=='highBoard')
        {
          $vol = new Volunteer;
          $user->save();
          $vol->user_id = $user->id;
          $vol->status_id = Status::where('name','deactivated')->value('id');
          $committee = Committee::query()->findOrFail($request->input('committee'));
          // dircetor of this committee of that season which is active exists
          // volunteer =>position director => of this commitee  => where this season is active

          $seasonId = Season::where('isActive',1)->value('id');
          $director = DB::table('vol_committees')
            ->join('volunteers', function ($join) {
            $join->on( 'vol_committees.vol_id', '=', 'volunteers.id')
                 ->where('vol_committees.season_id', '=', $seasonId)
                 ->where('committee_id','=',$committee->id);
        })->get();

            if ($director)
            {
                return response()->json(['message'=>'This Committee Already Have Director. If You Already The Right Director For This Committee Contact With the chairperson']);
            } else {
            $vol->position_id = Position::where('name','Director')->value('id');
            $vol->save();
            // $volComm = DB::table('vol_committees')->insertGetId(
            //   [
            //     'vol_id' => $vol->id,
            //     'committee_id' => $committee->id,
            //     'season_id' => $seasonId,
            //   ]
            // );
            //
            // $volHis = DB::table('vol_history')->insertGetId(
            //   [
            //     'vol_id' =>$vol->id,
            //     'season_id' =>$seasonId,
            //     'position_id' => Position::where('name','Director')->value('id'),
            //   ]
            // );
            }
        }

        // if ( $request->input('position')!='EX_com' && ($request->input('position') =='highBoard'))
        // {
        //     $user->save();
        //     $user->refresh();
        //     $committee->director_id = $user->id;
        //     $committee->director = $user->firstName . ' ' . $user->lastName;
        //     $committee->update();
        // }

        if ($request->input('role')=='volunteer')
        {
          $seasonId = Season::where('isActive',1)->value('id');
          $vol = new Volunteer;
          $user->save();
          $vol->position_id = Position::where('name','Volunteer')->value('id');
          $vol->status_id = Status::where('name','deactivated')->value('id');
          $vol->save();
          // $volComm = DB::table('vol_history')->insertGetId(
          //   [
          //     'vol_id'=>$vol->id,
          //     'season_id' =>$seasonId,
          //     'position_id' = > Position::where('name','Volunteer')->value('id'),
          //   ]
          // );
        }
        $us = $vol;
      }
      else {
        $par = new Participant();
        $user->save();
        $par->user_id = $user->id;
        $par->save();
        $us = $par;
      }
        // send activation email

          Mail::send('/emails.verify', compact(['type', 'req', 'user','confirmation_code']), function($message) use ($req,$us) {
              $message->to($this->MailTarget($req,$us), 'user')->subject('Verify an email address');
          });
        if ($user->id) {
            return response()->json(['response' => 'success', 'message' => 'Registration is Successful, please wait until your account being activated']);
        }else{
            return response()->json(['response' => 'failed', 'message' => 'Registration has failed, please check your data again!']);
        }
    }


    //  mail target
    public function MailTarget(Request $request, $us)
    {
        $email =  'ieeehelwanstudentbranch@gmail.com';

        // if Ex-com(Chairperson) register
        if ($request->input('type') == 'participant') {
          $user = User::query()->findOrFail($us->user_id);
          $email = $user->email;
        }
      elseif($request->input('type') == 'volunteer')
        {
        if ($request->input('role')=='EX_com' && ($request->input('ex_options')=='chairperson') ){
            $email = 'ieeehelwanstudentbranch@gmail.com';
        }

        // if Ex-com(!Chairperson) register
        if ($request->input('role')=='EX_com' && ($request->input('ex_options')!='chairperson') ) {
            try {
              // user id of the volunteer who is chairperson of the season which is active
              $seasonId = Season::where('isActive',1)->value('id');
              $pos = Position::where('name','Chairperson')->value('id');
              $chairperson = DB::table('volunteers')
              ->join('vol_history', function ($join) {
            $join->on('volunteers.id', '=', 'vol_history.vol_id')
            ->where('vol_history.season_id',$seasonId)
            ->where('volunteers.position_id', $pos);
        })->get();
                $user = User::query()->findOrFail($chairperson->user_id);
                $email = $user->email;
            } catch (\Exception $e) {
                $email = 'ieeehelwanstudentbranch@gmail.com';
            }
        }

        // if High Board register
        if ($request->input('role')=='highBoard') {
            try {
                $committee = Committee::query()->findOrFail($request->input('committee'));
                $pos = Position::where('name','Mentor')->value('id');
                $mentor = DB::table('volunteers')
                ->join('vol_committees', function ($join) {
              $join->on('volunteers.id', '=', 'vol_committees.vol_id')
              ->where('vol_committees.season_id',$seasonId)
              ->where('vol_committees.committee_id', $committee->id)
              ->where('volunteers.position_id', $pos);
          })->get();

                $mentor =User::query()->findOrFail($mentor->user_id);
                $email = $mentor->email;

            } catch (\Exception $e) {
                $email = 'ieeehelwanstudentbranch@gmail.com';
            }
        }

        // if volunteer register
        if ($request->input('role')=='volunteer') {
            try {
                $committee = Committee::query()->findOrFail($request->input('committee'));
                if ($committee->director_id != null) {
                    $director = User::query()->findOrFail($committee->director_id);
                    $email = $director->email;
                }elseif (User::query()->findOrFail($committee->mentor_id) != null) {
                    $mentor = User::query()->findOrFail($committee->mentor_id);
                    $email = $mentor->email;
                }else{
                    $email = 'ieeehelwanstudentbranch@gmail.com';
                }
            } catch (\Exception $e) {
                $email = 'ieeehelwanstudentbranch@gmail.com';
            }
        }
      }

        return $email;
    }
}
