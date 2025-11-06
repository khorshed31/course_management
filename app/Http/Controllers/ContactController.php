<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactMessageMailable;
use App\Models\ContactMessage;

class ContactController extends Controller
{
    public function send(Request $request)
    {
        $data = $request->validate([
            'name'    => ['required','string','max:120'],
            'email'   => ['required','email','max:190'],
            'phone'   => ['nullable','string','max:60'],
            'social'  => ['nullable','string','max:255'],
            'message' => ['required','string','max:5000'],
        ]);

        $adminEmail = config('mail.admin_address', env('MAIL_ADMIN'));
        if (!$adminEmail) {
            return back()->withErrors(['email' => 'Admin email is not configured. Set MAIL_ADMIN in .env'])
                         ->withInput();
        }

        // Add meta fields
        $data['ip'] = $request->ip();
        $data['user_agent'] = (string) $request->header('User-Agent');

        DB::transaction(function () use ($data, $adminEmail) {
            // store in DB
            $message = ContactMessage::create($data);

            // email to admin (include DB id for reference)
            Mail::to($adminEmail)->send(
                (new ContactMessageMailable($data + ['id' => $message->id]))
            );

            // ✅ Confirmation to the sender
            // Mail::to($data['email'])->send(
            //     new ContactMessageMailable($data + ['id' => $message->id], true)
            // );
            
        });

        return back()->with('status', 'Thanks! Your message has been sent.');
    }
}
