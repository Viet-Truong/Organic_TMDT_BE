<?php

namespace App\Http\Controllers\Client_api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\JsonResponse;
use Illuminate\Support\Facades\Mail;
use App\Mail\VerificationMail;

class AuthController extends Controller
{

    // Verify Token
    public function verifyToken($token)
    {
        $user = User::where('verification_token', $token)->first();
        if ($user) {
            $user->email_verified_at = now();
            $user->verification_token = null;
            $user->save();

            return response()->json([
                'message' => 'Email verified successfully',
                'type' => 'success',
                'data' => $user
            ]);
        }
        return response()->json([
            'message' => 'Invalid verification token',
            'type' => 'error'
        ]);
    }
    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|max:255|email',
            'password' => 'required',
            'phone_number' => 'required|string',
            'role' => 'required|string'
        ]);

        if($user = User::where('email', $request->email)->first()) {
            return response()->json([
                'message' => 'The email has already been taken',
                'type' => 'error',
            ]);
        }

        $data['verification_token'] = Str::random(20);
        $status = $request->input('status', 'Hoạt động');

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'phone_number' => $data['phone_number'],
            'role' => $data['role'],
            'status' => $status,
            'verification_token' => $data['verification_token'],
        ]);

        // Gửi email với token
        Mail::to($user->email)->send(new VerificationMail($user));

        return response()->json([
            'message' => 'User Registered',
            'type' => 'success',
            'data' =>  $user,
        ]);
    }

    public function logout(Request $request) {
        if ($request->user()) { 
            $request->user()->tokens()->delete();
        }
    
        return response()->json(['message' => 'Logged out'], 200);
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|string|max:255|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $data['email'])->first();
        
        if($user && $user->exists()) {
            if($user->email_verified_at == null) {
                return response()->json([
                    'message' => 'Account has not been verified',
                    'type' => 'verify'
                ]);
            } 
            else {
                if(password_verify($data['password'], $user->password)) {
                    return response()->json([
                        'message' => 'Login successfully',
                        'type' => 'success',
                        'data' => $user
                    ]);
                }
                else {
                    return response()->json(['message' => 'Password wrong']);
                }
            }
        }
        else {
            return response()->json(['message' => 'Not found account']);
        }
    }

    public function refresh(Request $request): JsonResponse
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'user' => $request->user(),
            'token' => $request->user()->createToken('api')->plainTextToken,
        ]);
    }

}
