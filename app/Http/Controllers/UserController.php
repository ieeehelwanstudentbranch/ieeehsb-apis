<?php

namespace App\Http\Controllers;

use App\Http\Resources\User\UserData;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserController extends Controller
{
    public function __construct()
    {

        $this->middleware('jwt.auth');

    }

    public function updateProfilePage($id){
        if($id == JWTAuth::parseToken()->authenticate()->id) {
            $user = User::findOrFail($id);
            return new UserData($user);
        }else{
            return response()->json('Un Authenticated');
        }
    }

    public function updateProfile(Request $request , $id){

        if($id == JWTAuth::parseToken()->authenticate()->id) {
            $this->validate($request ,[
                'firstName' => 'required|string | max:50 | min:3',
                'lastName' => 'required|string | max:50 | min:3',
                'email' => 'required|string|email|max:255',
                'DOB' => 'nullable',
                'faculty' => 'nullable|string | max:30 | min:3',
                'level' => 'nullable|numeric | max:30 | min:3',
                'university' => 'nullable|string | max:30 | min:3',
                'phone' => 'nullable|regex:/(01)[0-9]{9}/',
                'address' => 'nullable|string | max:100 | min:3',
                'profile_image' => 'image|nullable|max:1024 | mimes:jpg,png,jpeg,svg',
            ]);

            if ($request->input('password')) {
                $this->validate($request, [
                    'password' => 'required|confirmed|string|min:6',
                    'password_confirmation' => 'sometimes|required_with:password'
                ]);
            }
            $user = User::findOrFail($id);
            $user->firstName= $request->input('firstName');
            $user->lastName= $request->input('lastName');
            if ($request->input('faculty')){ $user->faculty= $request->input('faculty');}
            if ($request->input('university')){$user->university= $request->input('university');}
            if ($request->input('DOB')){ $user->DOB= $request->input('DOB');}
            if ($request->input('phone')){ $user->phone= $request->input('phone');}
            if ($request->input('level')){ $user->level= $request->input('level');}
            if ($request->input('address')){ $user->address= $request->input('address');}
            $user->email=$request->input('email');
            if ($request->input('password')) {
                $user->password = Hash::make($request->input('password'));
            }
            $user->email = $request->input('email');
            //upload image
            if ($request->hasFile('profile_image')) {
                $filenameWithExtention = $request->file('profile_image')->getClientOriginalName();
                $fileName = pathinfo($filenameWithExtention, PATHINFO_FILENAME);
                $extension = $request->file('profile_image')->getClientOriginalExtension();
                $fileNameStoreImage = $fileName . '_' . time() . '.' . $extension;

                $request->file('profile_image')->move(base_path() . '/public/uploaded/profile_images/', $fileNameStoreImage);
                $user->profile_image = $fileNameStoreImage;
            }
            $user->update();
              return redirect('/user/'.$id)->with('success','User Updated');
        }else{
            return response()->json('error','Un Authenticated');
        }
    }

}
