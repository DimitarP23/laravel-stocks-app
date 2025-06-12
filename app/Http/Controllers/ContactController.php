<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function show()
    {
        return view('contact');
    }

    public function submit(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'min:10',
                'regex:/^[A-Za-z\s]+$/',
            ],
            'email' => 'required|email',
            'subject' => 'required|in:general,support,feedback',
            'message' => 'required|min:10',
        ], [
            'name.required' => 'Please enter your full name',
            'name.min' => 'Your full name must be at least 10 characters long',
            'name.regex' => 'Your name can only contain letters and spaces',
            'email.required' => 'Please enter your email address',
            'email.email' => 'Please enter a valid email address',
            'subject.required' => 'Please select a subject',
            'subject.in' => 'Please select a valid subject',
            'message.required' => 'Please enter your message',
            'message.min' => 'Message must be at least 10 characters long',
        ]);

       
        return redirect('/contact')
            ->with('success', 'Thank you for your message. We\'ll get back to you soon!');
    }
}
