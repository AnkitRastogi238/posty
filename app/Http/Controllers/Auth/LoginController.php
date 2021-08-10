<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Models\VerifyUser;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\ResetPassword;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;
use App\Mail\ForgetPasswordMail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class LoginController extends Controller
{
    public function __construct()
    {
        $this->middleware(['guest']);
    }

    public function index()
    {
        return view('auth.login');
    }

    public function validateLogin(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            if (Auth::user()->email_verified_at == null) {
                Auth::logout();
                return redirect('user.login')->with('message', 'Plaese verify your email to continue');
            }
            return redirect(route('dashboard'))->with('success', 'Logged in succesfully');
        } else {
            return redirect()->back()->with('error', 'Incorrect email or password');
        }
    }
    public function verifyEmail($token)
    {
        $verifiedUser = VerifyUser::where('token', $token)->first();
        if (isset($verifiedUser)) {
            $user = $verifiedUser->user;
            if (!$user->email_verified_at) {
                $user->email_verified_at = Carbon::now();
                $user->save();
                return redirect(route('user.login'))->with('success', 'Your email has been verified');
            } else {
                return redirect()->back()->with('info', 'Your email has already been verified');
            }
        } else {
            return redirect(route('user.login'))->with('error', 'Something went wrong!!');
        }
    }

    public function getForgetPassword()
    {
        return view('auth.forget_password');
    }
    public function postForgetPassword(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return redirect()->back()->with('error', 'User not found.');
        } else {
            $reset_code = Str::random(200);
            ResetPassword::create([
                'user_id' => $user->id,
                'reset_code' => $reset_code
            ]);

            Mail::to($user->email)->send(new ForgetPasswordMail($user->name, $reset_code));

            return redirect()->back()->with('success', 'We have sent you a password reset link.Please check your mail.');
        }
    }

    public function getResetPassword($reset_code)
    {
        $password_reset_code = ResetPassword::where('reset_code', $reset_code)->first();
        if (!$password_reset_code || Carbon::now()->subMinutes(50) > $password_reset_code->created_at) {
            return redirect()->route('getForgetPassword')->with('error', 'Invalid password reset link or link expired');
        } else {
            return view('emails.reset_password', compact('reset_code'));
        }
    }

    public function postResetPassword($reset_code, Request $request)
    {
        $password_reset_code = ResetPassword::where('reset_code', $reset_code)->first();

        if (!$password_reset_code || Carbon::now()->subMinutes(50) > $password_reset_code->created_at) {
            return redirect()->route('getForgetPassword')->with('error', 'Invalid password reset link or link expired');
        } else {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required',
                'password_confirmation' => 'required|same:password',
            ]);

            $user = User::find($password_reset_code->user_id);

            if ($user->email != $request->email) {
                return redirect()->back()->with('error', 'Enter Correct Email');
            } else {
                $password_reset_code->delete();
                $user->update([
                    'password' => Hash::make($request->password)
                ]);

                return redirect()->route('user.login')->with('success', 'Password succesfully reset');
            }
        }
    }
}
