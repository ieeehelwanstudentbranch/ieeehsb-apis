<?php

namespace App\Http\Controllers;

use App\Committee;
use App\Http\Resources\User\UserData;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('jwt.auth')->except('deleteUser');
    }

    public function index($id)
    {
        $user = User::findOrFail($id);
        return new UserData($user);
    }

    public function updateProfilePage($id)
    {
        if ($id == JWTAuth::parseToken()->authenticate()->id) {
            $user = User::findOrFail($id);
            return new UserData($user);
        } else {
            return response()->json('error', 'Un Authenticated');
        }
    }

    public function updateProfile(Request $request, $id)
    {

        if ($id == JWTAuth::parseToken()->authenticate()->id) {
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
        $user_id = decrypt($id);
        try{
        $user = User::query()->findOrFail($user_id);
        if ($user->position == 'highBoard' && !($user->committee->name == 'RAS' ||$user->committee->name == 'PES' || $user->committee->name =='WIE'))
        {
        $user->committee->director_id = null;
        $user->committee->director = null;
        $user->committee->update();
        }
        if ($user->position == 'EX_com')
        {
            $user->ex_com_option->delete();
        }

        $user->delete();
        return response()->json(['success' => 'Deleted Successfully']);
        } catch (\Exception $e)
        {
            return response()->json(['error' => 'User Not Found']);
        }
    }

}