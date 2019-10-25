<?php

namespace App\Http\Controllers\AuthApi;

use App\Ex_com_options;
use App\HighBoardOptions;
use Illuminate\Http\Request;
//use App\Http\Controllers\Controller;
use App\Http\Controllers\Controller as Controller;
use App\Http\Requests;
use Illuminate\Support\Facades\Mail;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Facades\JWTFactory;
use JWTAuthException;
use App\User;
use App\Post;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    protected $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        $this->validate($request, [
            'email' => 'required',
            'password' => 'required|string|min:8|regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{8,}$/',
        ]);
        $token = null;
        $expirationTime = env('JWT_TTL', 60 * 24 * 30);
        if ($request->input('remember_me') == true) {
            $expirationTime = env('JWT_TTL', 60 * 24 * 30);
        }
        // else{
        //     $expirationTime = env('ttl',3);
        // }
        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json([
                    'response' => 'Error',
                    'message' => 'Invalid Email or Password',
                ]);
            }
            if (!User::where('email', $request['email'])->first()->confirmed) {
                return response()->json([
                    'response' => 'Error',
                    'message' => 'Sorry your account does not been activated yet',
                ]);
            }

        } catch (JWTAuthException $e) {
            return response()->json([
                'response' => 'Error',
                'message' => 'Failed to create token',
            ]);
        }
        auth()->user()->remember_token = $token;
        auth()->user()->update();
        return response()->json([
            'response' => 'Success',
            'message' => 'You logged in successfully',
            'token' => $token,
            'expirationTime' => $expirationTime,
            'userId' => Auth::user()->id
        ]);
    }

    // Logout
    public function logout(Request $request)
    {
        $this->validate($request, [
            'token' => 'required'
        ]);
        try {
            JWTAuth::invalidate($request->token);
            return response()->json([
                'success' => true,
                'message' => 'You logged out Successfully'
            ]);
        } catch (JWTException $exception) {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, You cannot be logged out'
            ], 500);
        }
    }

    public function checkToken($user_id, $token)
    {
        $user = User::query()->where('id', $user_id);
        if (count($user->get()) > 0) {
            $user = $user->first();
            if ($user->remember_token == $token) {
                return response()->json([
                    'response' => 'Success',
                    'message' => 'You logged in Successfully'
                ]);
            } else {
                return response()->json([
                    'response' => 'Error',
                    'message' => 'Token Expired',
                ]);
            }
        } else {
            return response()->json([
                'response' => 'Error',
                'message' => 'User Not Found',
            ]);
        }
    }
}