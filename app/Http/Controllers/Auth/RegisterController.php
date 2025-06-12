<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class RegisterController extends Controller
{
    public function create()
    {
        return view('auth.register');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => [
                'required',
                'confirmed',
                Password::min(10)          // Minimum 10 characters
                    ->mixedCase()          // Require both uppercase and lowercase
                    ->numbers()            // Require at least one number
                    ->symbols()            // Require at least one symbol

            ],
        ], [
            'password.min' => 'The password must be at least 10 characters.',
            'password.mixed' => 'The password must include both uppercase and lowercase letters.',
            'password.numbers' => 'The password must include at least one number.',
            'password.symbols' => 'The password must include at least one symbol.',

        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),  // Automatically salted and hashed
        ]);

        auth()->login($user);

        return redirect('/')->with('status', 'Account created successfully!');
    }
}
