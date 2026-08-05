<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function show()
    {
        return view('contact', ['currentPage' => 'contact']);
    }

    public function submit(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:190'],
            'message' => ['required', 'string', 'max:5000'],
            // Honeypot field — invisible to real visitors via CSS, catches
            // basic bots without adding friction like a captcha.
            'website' => ['prohibited'],
        ], [
            'name.required' => 'Please tell us your name.',
            'email.required' => 'Please enter an email address so we can reply.',
            'email.email' => 'That email address doesn\'t look right — please double-check it.',
            'message.required' => 'Please enter a message.',
        ]);

        try {
            // Swap this for Mail::to(config('mail.contact_recipient'))->send(...)
            // once a Mailable + Bluehost SMTP credentials are configured.
            Log::info('ZABIDA contact form submission', $validated);

            return back()->with('contact_status', 'success')
                ->with('contact_message', 'Thanks, '.$validated['name'].' — your message has been sent. We\'ll get back to you soon.');
        } catch (\Throwable $e) {
            Log::error('ZABIDA contact form failed to send', ['error' => $e->getMessage()]);

            return back()->withInput()
                ->with('contact_status', 'error')
                ->with('contact_message', 'Something went wrong sending your message. Please try again, or email us directly at zabidamail.ph@gmail.com.');
        }
    }
}
