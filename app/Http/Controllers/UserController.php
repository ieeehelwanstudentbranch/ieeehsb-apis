<?php

namespace App\Http\Controllers;

use App\Committee;
use App\Volunteer;
use App\Http\Resources\User\UserData;
use App\Participant;
use App\User;
use App\Season;
use App\Position;
use App\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;



class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('jwt.auth')->except('changeUser');
    }

    /**
     * @SWG\Get(
     *   path="/api/user/{id}",
     *   summary="User Profile",
     *   operationId="profile",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=406, description="not acceptable"),
     *   @SWG\Response(response=500, description="internal server error"),
     @SWG\Parameter(
     *          name="id",
     *          in="path",
     *          description="user id",
     *          required=true,
     *          type="integer",
     *     ),
     @SWG\Parameter(
     *          name="Authorization",
     *          in="header",
     *          required=true,
     *          type="string",
     *          schema="Bearer",
     *          format="JWT",
     *     ),

     *   )
     **/
    public function show(User $user)
    {
        return new UserData($user);
    }

    public function edit(User $user)
    {
        if ($user->id == JWTAuth::parseToken()->authenticate()->id) {
            // $user = User::findOrFail($id);
            return new UserData($user);
        } else {
            return response()->json('error', 'Un Authenticated');
        }
    }

    public function update(Request $request, User $user)
    {

        if ($user->id == JWTAuth::parseToken()->authenticate()->id) {
            $this->validate($request, [
                'firstName' => 'required|string | max:50 | min:3',
                'lastName' => 'required|string | max:50 | min:3',
                'email' => 'required|string|email|max:255',
                'DOB' => 'nullable',
                'faculty' => 'nullable|string | max:30 | min:3',
                'level' => 'nullable|numeric | max:30 | min:3',
                'university' => 'nullable|string | max:30 | min:3',
                'phone' => 'nullable|regex:/(01)[0-9]{9}/',
                'address' => 'nullable|string | max:100 | min:3'
            ]);

            $user = User::findOrFail($id);
            $user->firstName = $request->firstName;
            $user->lastName = $request->lastName;
            $user->faculty = $request->faculty  ?? null;
            $user->university = $request->university  ?? null;
            $user->DOB = $request->DOB  ?? null;
            $user->address = $request->address  ?? null;
            $user->phone = $request->phone  ?? null;
            $user->level = $request->level  ?? null;
            $user->email = $request->email ;
            $user->update();
            return response()->json(['success' => 'user updated'], 200);
        } else {
            return response()->json(['error' => 'Un Authenticated']);
        }
    }

    public function updateProfilePassword(Request $request, $id)
    {
        if ($id == JWTAuth::parseToken()->authenticate()->id) {
            $this->validate($request, [
                'old_password' => 'required|string|min:6|regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{6,}$/',
                'new_password' => 'required|string|min:6|confirmed|regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{6,}$/',
                'new_password_confirmation' => 'sometimes|required_with:new_password'
            ]);
            $user = User::findOrFail($id);

            if (Hash::check($request->old_password , $user->password)) {
                $user->password = app('hash')->make($request->new_password);
                $user->update();
                return response()->json(['success' => 'PasswordUpdated']);
            } else {
                return response()->json(['error' => 'OldPasswordInvalid']);
            }
        } else {
            return response()->json(['error' => 'Un Authenticated']);
        }
    }

    public function updateProfileImage(Request $request, $id)
    {

        if ($id == JWTAuth::parseToken()->authenticate()->id) {
            $this->validate($request, [
                'profile_image' => 'image|nullable|max:500000 |mimes:jpg,png,jpeg,svg,gif,tiff,tif',
            ]);
            $user = User::findOrFail($id);
            //upload image
            $filename = $request->file('profile_image')->store('public/profile_images/');
            $user->image = trim($filename, 'public');
            $user->update();
            return response()->json(['success' => 'updated-successfully']);
        } else {
            return response()->json(['error' => 'un-authenticated']);
        }
    }

    public function changeUser($id)
    {

        $user_id =decrypt($id);
        try{
        $user = User::findOrFail($user_id);
        if($user->type == "particiapnt")
        {
            Participant::where('user_id',$user->id)->delete();
            $user->delete();


        }
          else {
            $vol = Volunteer::where('user_id',$user->id)->value('id');
            // dd($vol);
            $seasonId = Season::where('isActive',1)->value('id');
            DB::table('vol_history')->where('vol_id',$vol)->where('season_id',$seasonId)->delete();
            DB::table('vol_committees')->where('season_id',$seasonId)->where('vol_id',$vol)
            ->delete();
            Volunteer::findOrFail($vol)->delete();
            $user->type="particiapnt";
            $user->update();
            $par = new Participant;
            $par->user_id = $user->id;
            $par->save();
            $type = $user->type;
            $confirmation_code = $user->confirmation_code;
             Mail::send('/emails.verify', compact(['type', 'user','confirmation_code']), function($message) use ($user) {
            $message->to($user->email, 'user')->subject('Verify an email address');
        });
          }
        return response()->json(['success' => 'Deleted Successfully']);

        } catch (\Exception $e)
        {

            return response()->json(['error' => 'User Not Found']);
        }
    }

}
