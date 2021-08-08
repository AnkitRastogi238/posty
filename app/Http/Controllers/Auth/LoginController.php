<?php

namespace App\Http\Controllers\Auth;

use App\Models\VerifyUser;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

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
}
