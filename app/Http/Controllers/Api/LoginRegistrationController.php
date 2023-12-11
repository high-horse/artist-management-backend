<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

class LoginRegistrationController extends Controller
{
    //Registation API (POST, formdata)
    public function register(Request $request)
    {

        // Validation
        $request->validate([
            "name" => "required",
            "email" => "required|email|unique:users",
            "password" => "required|confirmed"
        ]);

        User::create([
            "name" => $request->name,
            "email" => $request->email,
            "password" => Hash::make($request->password),
        ]);

        return response()->json([
            "status" => true,
            "message" => "New User Created Successfully",
        ]);
    }

    // Login API (POST, formdata)
    public function login(Request $request)
    {
        // Validation
        $request->validate([
            "email" => "required|email",
            "password" => "required"
        ]);

        // JWT auth and attempt
        $token = JWTAuth::attempt([
            "email" => $request->email,
            "password" => $request->password,
        ]);

        // Response
        if (!empty($token)) {
            // $qry = 'select * from users where email=?'.$request->email;
            $user = User::where('email', $request->email)->first();
            return response()->json([
                "status" => true,
                "message" => "User logged in successfully.",
                "token" => $token,
                "userdata" => $user
            ]);
        }
        return response()->json([
            "status" => false,
            "message" => "Invalid ligin details."
        ]);
    }

    // Profile API (GET)
    public function profile()
    {

        $userdata = auth()->user();

        return response()->json([
            "status" => true,
            "message" => "Profile data",
            "user" => $userdata,
        ]);
    }

    // Refresh token API (GET) 
    public function refreshToken()
    {

        $newToken = auth()->refresh();
        // $newToken = "123";

        return response()->json([
            "status" => true,
            "message" => "New token generated.",
            "token" => $newToken,
        ]);
    }

    // Logout API (GET)
    public function logout()
    {

        auth()->logout();

        return response()->json([
            "status" => true,
            "message" => "User loggod out successfully",
        ]);
    }

    // Get all users' data API (GET)
    public function fetch_all_userdata()
    {
        // Get all users from Users table
        $users = User::all();
        return response()->json([
            'status' => true,
            'message' => "All users's data fetched",
            'data' => $users,
        ]);
    }
}
