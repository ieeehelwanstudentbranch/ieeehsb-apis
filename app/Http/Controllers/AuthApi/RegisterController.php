<?php

namespace App\Http\Controllers\AuthApi;

use App\User;
use App\Committee;
use App\Status;
use App\Season;
use App\Role;
use App\Position;
use App\Volunteer;
use App\Participant;
use App\Http\Controllers\Controller as Controller;
use App\Http\Resources\Register\RegisterCollection;
use Egulias\EmailValidator\Exception\ExpectingCTEXT;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\DB;
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
/**
     * @SWG\Get(
     *   path="/api/register/",
     *   summary="Registeration Form View",
     *   operationId="register",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=406, description="not acceptable"),
     *   @SWG\Response(response=500, description="internal server error"),
     *)
     **/
    public function registerPage(){
        $data = Role::all();
        return new RegisterCollection($data);
    }
 /**
     * @SWG\Post(
     *   path="/api/register/",
     *   summary="Add new user",
     *   operationId="register",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=406, description="not acceptable"),
     *   @SWG\Response(response=500, description="internal server error"),
     *@SWG\Parameter(
     *          name="firstName",
     *          in="query",
     *      description="testing data",
     *          required=true,
     *          type="string",
     *     ),
     *@SWG\Parameter(
     *          name="lastName",
     *          in="query",
     *      description="testing data",
     *          required=true,
     *          type="string",
     *     ),
     *@SWG\Parameter(
     *          name="facutly",
     *          in="query",
     *      description="testing data",
     *          required=true,
     *          type="string",
     *     ),
     *@SWG\Parameter(
     *          name="university",
     *          in="query",
     *      description="testing data",
     *          required=true,
     *          type="string",
     *     ),
     *@SWG\Parameter(
     *          name="DOB",
     *          in="query",
     *      description="testing data",
     *          required=true,
     *          type="string",
     *     ),
     *@SWG\Parameter(
     *          name="email",
     *          in="query",
     *      description="testing data",
     *          required=true,
     *          type="string",
     *     ),
     *@SWG\Parameter(
     *          name="type",
     *          in="query",
     *      description="testing data",
     *          required=true,
     *          type="integer",
     *     ),
     *@SWG\Parameter(
     *          name="password",
     *          in="query",
     *      description="testing data",
     *          required=true,
     *          type="string",
     *     ),
     *@SWG\Parameter(
     *          name="password_confirmation",
     *          in="query",
     *      description="testing data",
     *          required=true,
     *          type="string",
     *     ),
     *@SWG\Parameter(
     *          name="role",
     *          in="query",
     *      description="testing data",
     *          required=false,
     *          type="string",
     *     ),
     *@SWG\Parameter(
     *          name="committee",
     *          in="query",
     *      description="testing data",
     *          required=false,
     *          type="string",
     *     ),
     *@SWG\Parameter(
     *          name="ex_options",
     *          in="query",
     *      description="testing data",
     *          required=false,
     *          type="string",
     *     ),


     *   )


     *
     */
    public function register(Request $request)
    {
        $req = $request;
        $type = $request->type;
        // Input::merge(array_map('trim', Input::all()));
        $validator = Validator::make($request->all(), [
            'firstName' => 'required |string | max:50 | min:3',
            'lastName' => 'required |string | max:50 | min:3',
            'faculty' => 'nullable |string | max:30 |   min:3',
            'university' => 'nullable |string | max:30 | min:3',
            'DOB' => 'nullable|date_format:d-m-Y|before:today',
            'image' => 'image|nullable|max:500000 |mimes:jpg,png,jpeg,svg,gif,tiff,tif',
            'email' => 'required |string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed|regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9]).{6,}$/',
            'password_confirmation'=>'required_with:password',
            'type' =>'required|string',
        ]);

        if ($request->type=='volunteer')
        {$validator = Validator::make($request->all(), ['role' => 'numeric|required',
            'password' => 'required|string|min:6|confirmed|regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9]).{6,}$/',
            'password_confirmation'=>'required_with:password']);}

       // if ($request->input('role')=='ex_com')
       // {$validator = Validator::make($request->all(), ['ex_options' => 'required']);}

       if ($validator->fails()) {

         return response()->json(['errors'=>$validator->errors()]);
       }

         //if position EX-com
//         DB::beginTransaction();
         if (User::where('email',$request->email)->first() != null) {
           return response()->json(['errors'=> "The email is stored before"]);
         }
        $confirmation_code = str_random(30);
        $user= new User();
        $user->firstName= $request->firstName;
        $user->lastName= $request->lastName;
        $user->image = 'default.png';
        $user->faculty= $request->faculty;
        $user->university= $request->university;
        $user->DOB= $request->DOB;
        $user->email=$request->email;
        $user->confirmation_code  = $confirmation_code ;
        $user->password=app('hash')->make($request->password);

        if ($request->type== 'volunteer'){
            $role = Role::find($request->role);

            if ($role->name !='ex_com' )
          {
              $validat = Validator::make($request->all(), ['committee' => 'numeric| required']);
         if ($validat->fails()) {

            return response()->json(['errors'=>$validat->errors()]);
          }
         elseif (! Committee::where('id',$request->committee)->first())
              {
                  return response()->json(['message'=>'the committee is not found']);
              }
        }
          $seasonId = Season::where('isActive',1)->value('id');
           $stat    = Status::where('name','deactivated')->value('id');
          if ($role->name =='ex_com'){
            if($request->ex_options != null)
            {
            $vol = new Volunteer();
            $user->type = "volunteer";
            $user->save();
            $vol->user_id = $user->id;
            $vol->status_id = $stat;

              $vol->position_id = $request->ex_options;
              $vol->save();
              $volHis = DB::table('vol_history')->insertGetId(
                [
                  'vol_id' =>$vol->id,
                  'season_id' =>$seasonId,
                  'position_id' => $request->ex_options,
                ]
              );
                $pos = Position::find($request->ex_options)->name;

            }
            else {
              return response()->json(['message'=>'Ex Options Is Required And Must Be A Number']);

            }
        }

        elseif ($role->name =='highboard')
        {

          $vol = new Volunteer;
          $user->type = "volunteer";

          $seasonId = Season::where('isActive',1)->value('id');
//          $committee = DB::table('committees')->where('name',($request->committee))->value('id');
//          $dirPos = Position::where('name','director')->value('id');
          $status = Status::where('name','activated')->value('id');
          // dircetor of this committee of that season which is active exists
          // volunteer =>position director => of this commitee  => where this season is active

          $director = DB::table('vol_committees')
            ->join('volunteers', 'vol_committees.vol_id', '=', 'volunteers.id')
                 ->where('vol_committees.season_id', '=',  $seasonId)
                 ->where('vol_committees.committee_id','=',$request->committee)
                 ->where('vol_committees.position','=','director' )
                 ->where('volunteers.status_id' ,'=',$status)->first();
            if ($director != null)
            {
                return response()->json(['message'=>'This Committee Already Have Director. If You Already The Right Director For This Committee Contact With the chairperson']);
            } else {
              $user->save();
              $vol->user_id = $user->id;
              $vol->status_id = $stat;
            $vol->position_id = Position::where('name','director')->value('id');
            $vol->status_id = $stat;
            $vol->save();
            $volComm = DB::table('vol_committees')->insertGetId(
              [
                'vol_id' => $vol->id,
                'committee_id' => $request->committee,
                'season_id' => $seasonId,
                'position' => 'director'
              ]
            );

            $volHis = DB::table('vol_history')->insertGetId(
              [
                'vol_id' =>$vol->id,
                'season_id' =>$seasonId,
                'position_id' => Position::where('name','director')->value('id'),
              ]
            );

            }
            $pos = Committee::find($request->committee)->name;
        }

        elseif ($role->name=='volunteer')
        {
//          $committee = DB::table('committees')->where('name',($request->committee))->value('id');
          $vol = new Volunteer;
          $user->type = "volunteer";
          $user->save();
          $vol->user_id = $user->id;
          $vol->position_id = Position::where('name','volunteer')->value('id');
          $vol->status_id = $stat;
          $vol->save();
          $volHis = DB::table('vol_history')->insertGetId(
            [
              'vol_id'=>$vol->id,
              'season_id' =>$seasonId,
              'position_id' => Position::where('name','volunteer')->value('id'),
            ]
          );
          $volComm = DB::table('vol_committees')->insertGetId(
            [
              'vol_id' => $vol->id,
              'committee_id' => $request->committee,
              'season_id' => $seasonId,
              'position' => 'volunteer',
            ]
          );
          $pos = 'Volunteer';
        }
      }
      else {
        $par = new Participant();
          $user->type = "participant";
        $user->save();
        $par->user_id = $user->id;
        $par->save();
      }
//      DB::commit();

        // send activation email
        Mail::send('/emails.verify', compact(['type', 'role','req', 'pos', 'user','confirmation_code']), function($message) use ($req,$user) {
            $message->to($this->MailTarget($req,$user), 'user')->subject('Verify an email address');
        });

        if ($user->id) {
            return response()->json(['response' => 'success', 'message' => 'Registration is Successful, please wait until your account being activated']);
        }else{
            $type = $user->ptype();
            $type->delete();
            $user->delete();
            return response()->json(['response' => 'failed', 'message' => 'Registration has failed, please check your data again!']);
        }
    }

    public function pos ($pos){
        Position::find($pos)->name;
    }
    //  mail target
    public function position($pos,$comm)
    {
        $ment = DB::table('volunteers')
            ->join('vol_committees', function ($join) use ($comm,$pos) {
                $join->on('volunteers.id', '=', 'vol_committees.vol_id')
                    ->where('vol_committees.season_id',Season::where('isActive',1)->value('id'))
                    ->where('vol_committees.committee_id', $comm)
                    ->where('vol_committees.position', $pos);
            })->get();

    }
    public function MailTarget(Request $request, $user)
    {
      $email = 'ieeehelwanstudentbranch@gmail.com';

        // if participant register
        if ($user->type == 'participant') {
          $email = $user->email;
        }
      elseif($user->type == 'volunteer')
        {
            $role = Role::find($request->role);

          // $committee = DB::table('committees')->where('name',($request->input('committee')))->value('id');



        if ($role->name =='ex_com' && ($request->ex_options==1) ){
          $email = 'ieeehelwanstudentbranch@gmail.com';
        }

        // if Ex-com(!Chairperson) register
        if ($role->name=='ex_com' && ($request->ex_options!=1) ) {
          $seasonId = Season::where('isActive',1)->value('id');
          $status = Status::where('name','activated')->value('id');
          $pos = Position::where('name','chairperson')->value('id');
           if(Volunteer::where('position_id',$pos )->where('status_id',$status)->first() != null)
           {
              // user id of the volunteer who is chairperson of the season which is active
//              $chairperson = DB::table('volunteers')
//                           ->join('vol_history', function ($join) use ($seasonId) {
//                           $join->on('volunteers.id', '=', 'vol_history.vol_id')
//                            ->where('vol_history.season_id',$seasonId)
//                            ->where('volunteers.position_id',  Position::where('name','chairperson')->value('id'));
//                           })->get();
                $chairperson = Volunteer::where('position_id',$pos )->where('status_id',$status)->first();

                $user = User::find($chairperson->user_id);
                $email = $user->email;
            }
           else {
              $email = 'ieeehelwanstudentbranch@gmail.com';
            }
        }

        // if High Board register
        if ($role->name=='highboard') {

//          $committee = Committee::where('name', ($request->committee))->value('id');

          $ment = self::position('mentor',$request->committee);
            $status = Status::where('name','activated')->value('id');
            $pos = Position::where('name','chairperson')->value('id');
                if ($ment != null)
                {
                    $mentor = User::query()->findOrFail($ment->user_id);
                    $email = $mentor->email;
                }
                elseif(Volunteer::where('position_id',$pos )->where('status_id',$status)->first() != null)
                {
                    $chairperson = Volunteer::where('position_id',$pos )->where('status_id',$status)->first();
                    $user = User::query()->findOrFail($chairperson->user_id);
                    $email = $user->email;
                }
                else{

                $email = 'ieeehelwanstudentbranch@gmail.com';
                 // $email = 'engMarina97@gmail.com';

            }
        }

        // if volunteer register
        if ($request->role=='volunteer') {
            $dir = self::position('director',$request->committee);


                if (User::find($dir->user_id) != null) {
                    $director = User::find($dir->user_id);
                    $email = $director->email;
                }


                elseif (User::find($ment->user_id) != null) {
                    $ment = self::position('mentor',$request->committee);

                    $mentor = User::find($ment->user_id);
                    $email = $mentor->email;
                }else{
                  $email = 'ieeehelwanstudentbranch@gmail.com';
                }

        }
      }

        return $email;
    }
}
