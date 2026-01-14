<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class ForgotPasswordController extends Controller
{
    // Form nhập email
    public function showForgotForm()
    {
        return view('pages.checkout.forgot_password');
    }

    // Gửi OTP
    public function sendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email'
        ], [
            'email.exists' => 'Email chưa được đăng ký'
        ]);

        $otp = rand(100000, 999999);

        DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->delete();

        DB::table('password_reset_tokens')->insert([
            'email' => $request->email,
            'token' => Hash::make($otp),
            'created_at' => Carbon::now(),
        ]);

        Mail::raw(
            "Mã OTP đặt lại mật khẩu của bạn là: $otp\nMã có hiệu lực trong 10 phút.",
            function ($message) use ($request) {
                $message->to($request->email)
                        ->subject('OTP đặt lại mật khẩu');
            }
        );

        return redirect()
            ->route('password.reset')
            ->with('email', $request->email)
            ->with('success', 'OTP đã được gửi vào email');
    }

    // Form nhập OTP
    public function showResetForm()
    {
        if (!session('email')) {
            return redirect()->route('password.forgot');
        }

        return view('pages.reset_password', [
            'email' => session('email')
        ]);
    }

    // Xử lý đổi mật khẩu
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email'    => 'required|email|exists:users,email',
            'otp'      => 'required|digits:6',
            'password' => 'required|min:6|confirmed'
        ]);

        $record = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (!$record || !Hash::check($request->otp, $record->token)) {
            return back()->withErrors(['otp' => 'OTP không đúng']);
        }

        DB::table('users')
            ->where('email', $request->email)
            ->update([
                'password' => Hash::make($request->password)
            ]);

        DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->delete();

        return redirect('/login-checkout')
            ->with('success', 'Đặt lại mật khẩu thành công');
    }
}
