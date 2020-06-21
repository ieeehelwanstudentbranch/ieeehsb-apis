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


class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('jwt.auth')->except('deleteUser');
    }

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
            $user->firstName = $request->input('firstName');
            $user->lastName = $request->input('lastName');
            $user->faculty = $request->input('faculty') ?? null;
            $user->university = $request->input('university') ?? null;
            $user->DOB = $request->input('DOB') ?? null;
            $user->address = $request->input('address') ?? null;
            $user->phone = $request->input('phone') ?? null;
            $user->level = $request->input('level') ?? null;
            $user->email = $request->input('email');
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

            if (Hash::check($request->input('old_password'), $user->password)) {
                $user->password = app('hash')->make($request->input('new_password'));
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

    public function deleteUser($id)
    {
        $user_id = $id;
        $user = User::findOrFail($user_id);
        try{
          $par = Participant::where('user_id',$user->id)->get();
          if($par->count() >0)
          {
            Participant::where('user_id',$user->id)->delete();

          }
          else {
            $vol = Volunteer::where('user_id',$user->id)->value('id');
            $seasonId = Season::where('isActive',1)->value('id');
            DB::table('vol_history')->where('vol_id',$vol)->where('season_id',$seasonId)->delete();
            DB::table('vol_committees')->where('season_id',$seasonId)->where('vol_id',$vol)->delete();
            Volunteer::findOrFail($vol)->delete();
          }
          $user->delete();
        return response()->json(['success' => 'Deleted Successfully']);
    
        } catch (\Exception $e)
        {

            return response()->json(['error' => 'User Not Found']);
        }
    }

}
