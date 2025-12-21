<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function send(Request $request)
    {

        $request->validate([
            'name'    => 'required',
            'email'   => 'required|email',
            'message' => 'required',
        ]);

        Mail::send('emails.contact', [
            'name'    => $request->name,
            'email'   => $request->email,
            'content' => $request->message,
        ], function ($mail) use ($request) {
            $mail->to('unkstore@gmail.com')
                 ->subject('Liên hệ mới từ website')
                 ->replyTo($request->email, $request->name);
        });

        return back()->with('success', 'Gửi liên hệ thành công!');
    }
}
