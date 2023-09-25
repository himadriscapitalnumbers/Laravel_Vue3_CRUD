<?php

namespace App\Http\Controllers\Api\Auth;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\PasswordReset;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class ForgotPasswordController extends Controller
{
    
    
    public function createResetPasswordToken(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users',
        ]);

        $token = Str::random(64);

        try
        {
        PasswordReset::updateOrCreate(
            ['email' => $request->email],
            ['token' => $token]
        );

        Mail::send('forgetPassword', ['token' => $token], function($message) use($request){
            $message->to($request->email);
            $message->subject('Reset Password');
        });

        return response()->json(['success' => $token]);
        }
        catch(e)
        {
            return response()->json(['error' => 1]);
        }
    }

    public function validatePasswordToken(Request $request)
    {
        $valid = PasswordReset::where('token',$request->token)->count();
        
        if($valid > 0)
        {
            return response()->json(['valid' => $valid]);
        }
        else{

            return response()->json([], 401); 

        } 

    }

    public function resetpassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users',
            'password' => 'required|confirmed',
            'token' => 'required'
        ]);


        $valid = PasswordReset::where('token',$request->token)->where('email',$request->email)->count();

        if($valid > 0)
        {
            
            $updated = User::where('email', $request->email)->update(['password' => Hash::make($request->password)]);

            if($updated)
            {
                PasswordReset::where('token',$request->token)->delete();

                return response()->json(['success' => 1]);
            }
        }

        return response()->json([], 401); 


    }

    
}
