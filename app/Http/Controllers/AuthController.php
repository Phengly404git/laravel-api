<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator as Phengly;;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Phengly::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:5',
            'confirm_password' => 'required|same:password'
        ]);

        if ($validator->passes()) {
            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->save();
            $token = Auth::user();
            $token = $user->createToken("API")->plainTextToken;
            return response()->json(
                [
                    'status' => true,
                    'user' => $user,
                    'token' => $token,
                    'message' => 'User registered successfully'
                ],
                201
            );
        } else {
            return response()->json(
                [
                    'status' => false,
                    'message' => 'User registration failed',
                    'error' => $validator->errors()
                ],
                500
            );
        }
    }
    public function login(Request $request)
    {
        $validator = Phengly::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if ($validator->passes()) {
            $credentials = [
                'email' => $request->email,
                'password' => $request->password
            ];
            if (Auth::attempt($credentials)) {
                $user = Auth::user();
                $token = $user->createToken("API")->plainTextToken;
                return response()->json(
                    [
                        'status' => true,
                        'user' => $user,
                        'message' => 'User logged in successfully',
                        'token' => $token
                    ],
                    200
                );
            } else {
                return response()->json(
                    [
                        'status' => false,
                        'message' => 'Invalid email or password'
                    ],
                    500
                );
            }
        } else {
            return response()->json(
                [
                    'status' => false,
                    'message' => 'Invalid credentials',
                    'error' => $validator->errors()
                ],
                500
            );
        }
    }


    public function logout(Request $request)
    {
        $user = $request->user();
        $user->tokens()->delete();
        return response()->json(
            [
                'status' => true,
                'message' => 'User logged out successfully'
            ],
            200
        );
    }
}
