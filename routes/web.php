<?php

use Illuminate\Support\Facades\Route;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

// BASIC ROUTES
Route::get('/', function () {
    return view('home');
});

Route::get('/about', function () {
    return view('about');
});

// COMPLETELY RAW TEST - NO WEB MIDDLEWARE AT ALL
Route::get('/test', function () {
    return '<html><body><h1>Raw Test (No Middleware)</h1><form method="POST" action="/test-post"><button type="submit">Test Submit (No Sessions, No CSRF)</button></form></body></html>';
})->withoutMiddleware(['web']);

Route::post('/test-post', function (Request $request) {
    return 'SUCCESS! No 419 error. Data: ' . json_encode($request->all());
})->withoutMiddleware(['web']);

// RAW REGISTRATION - BYPASS ALL MIDDLEWARE
Route::get('/register', function () {
    return '
    <html>
    <head><title>Raw Register</title></head>
    <body>
        <h1>Raw Registration (No Sessions/CSRF)</h1>
        <form method="POST" action="/register-raw">
            <input type="text" name="name" placeholder="Name" required><br><br>
            <input type="email" name="email" placeholder="Email" required><br><br>
            <input type="password" name="password" placeholder="Password" required><br><br>
            <button type="submit">Register</button>
        </form>
    </body>
    </html>';
})->name('register')->withoutMiddleware(['web']);

Route::post('/register-raw', function (Request $request) {
    try {
        $name = $request->input('name');
        $email = $request->input('email');
        $password = $request->input('password');

        if (empty($name) || empty($email) || empty($password)) {
            return 'ERROR: All fields required';
        }

        if (User::where('email', $email)->exists()) {
            return 'ERROR: Email already exists';
        }

        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
        ]);

        return 'SUCCESS! User created: ' . $user->name . ' (' . $user->email . ')';
    } catch (Exception $e) {
        return 'EXCEPTION: ' . $e->getMessage();
    }
})->withoutMiddleware(['web']);

// RAW LOGIN - BYPASS ALL MIDDLEWARE
Route::get('/login', function () {
    return '
    <html>
    <head><title>Raw Login</title></head>
    <body>
        <h1>Raw Login (No Sessions/CSRF)</h1>
        <form method="POST" action="/login-raw">
            <input type="email" name="email" placeholder="Email" required><br><br>
            <input type="password" name="password" placeholder="Password" required><br><br>
            <button type="submit">Login</button>
        </form>
    </body>
    </html>';
})->name('login')->withoutMiddleware(['web']);

Route::post('/login-raw', function (Request $request) {
    try {
        $email = $request->input('email');
        $password = $request->input('password');

        if (empty($email) || empty($password)) {
            return 'ERROR: Email and password required';
        }

        $user = User::where('email', $email)->first();

        if ($user && Hash::check($password, $user->password)) {
            return 'SUCCESS! Login successful for: ' . $user->name;
        }

        return 'ERROR: Invalid credentials';
    } catch (Exception $e) {
        return 'EXCEPTION: ' . $e->getMessage();
    }
})->withoutMiddleware(['web']);

// STOCKS WITH MINIMAL MIDDLEWARE
Route::get('/stocks', function (Request $request) {
    $stocks = \App\Models\Stock::all();
    return 'STOCKS: ' . json_encode($stocks->toArray());
})->withoutMiddleware(['web']);

// Test route for 500 error
Route::get('/test-500', function () {
    throw new Exception('Test exception for 500 error page');
});
