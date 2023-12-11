<?php

namespace App\Http\Controllers\Api;

use App\Models\PasswordReset;
use App\Models\User;
use App\Mail\PasswordReset as ResetPasswordMail;
use Carbon\Carbon;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ResetPasswordController extends Controller
{
    //send mail
    public function sendMail(Request $request)
    {
        $user = User::where('email', $request->email)->first();

        if(!$user = User::where('email', $request->email)->first()) {
            return response()->json([
                'message' => 'Invalid Mail',
                'type' => 'error'
            ]);
        }

        $passwordReset = PasswordReset::updateOrCreate(
            ['email' => $user->email],
            ['token' => Str::random(60)]
        );

        if ($passwordReset) {
            Mail::to($request->email)->send(new ResetPasswordMail($passwordReset->token));
        }

        return response()->json([
            'message' => 'We have e-mailed your password reset token!',
            'type' => 'success'
        ]);
    }

    public function resetPassword(Request $request)
    {
        $passwordReset = PasswordReset::where('token', $request->token)->first();

        if($passwordReset == NULL) {
            return response()->json([
                'message' => 'This password reset token is invalid.',
                'type' => 'error'
            ]);
        }

        if (Carbon::parse($passwordReset->updated_at)->addMinutes(720)->isPast()) {
            $passwordReset->delete();

            return response()->json([
                'message' => 'This password reset token is invalid.',
                'type' => 'error'
            ]);
        }
        $user = User::where('email', $passwordReset->email)->firstOrFail();
        $user->update(['password' => bcrypt($request->input('password'))]);
        $passwordReset->delete();

        return response()->json([
            'message' => 'Change password was successfully',
            'type' => 'success',
            'data' => $user
        ]);
    }
}
