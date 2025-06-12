<?php

use Illuminate\Support\Facades\Route;
use App\Models\User;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cookie;

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

// AUTHENTICATION ROUTES (bypassing web middleware to avoid 419 errors)
Route::get('/register', function () {
    return view('auth.register');
})->name('register')->withoutMiddleware(['web']);

Route::post('/register', function (Request $request) {
    try {
        $name = $request->input('name');
        $email = $request->input('email');
        $password = $request->input('password');
        $password_confirmation = $request->input('password_confirmation');

        // Basic validation
        if (empty($name) || empty($email) || empty($password)) {
            return back()->withErrors(['email' => 'All fields are required.']);
        }

        if ($password !== $password_confirmation) {
            return back()->withErrors(['password' => 'Passwords do not match.']);
        }

        if (strlen($password) < 8) {
            return back()->withErrors(['password' => 'Password must be at least 8 characters.']);
        }

        if (User::where('email', $email)->exists()) {
            return back()->withErrors(['email' => 'Email already exists.']);
        }

        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
        ]);

        // Set user cookie for authentication
        $cookie = cookie('user_id', encrypt($user->id), 60 * 24 * 7); // 7 days
        return redirect('/stocks')->with('success', 'Registration successful!')->cookie($cookie);
    } catch (Exception $e) {
        return back()->withErrors(['email' => 'Registration failed. Please try again.']);
    }
})->withoutMiddleware(['web']);

Route::get('/login', function () {
    return view('auth.login');
})->name('login')->withoutMiddleware(['web']);

Route::post('/login', function (Request $request) {
    try {
        $email = $request->input('email');
        $password = $request->input('password');

        if (empty($email) || empty($password)) {
            return back()->withErrors(['email' => 'Email and password are required.']);
        }

        $user = User::where('email', $email)->first();

        if ($user && Hash::check($password, $user->password)) {
            // Set user cookie for authentication
            $cookie = cookie('user_id', encrypt($user->id), 60 * 24 * 7); // 7 days
            return redirect('/stocks')->with('success', 'Login successful!')->cookie($cookie);
        }

        return back()->withErrors(['email' => 'Invalid credentials.']);
    } catch (Exception $e) {
        return back()->withErrors(['email' => 'Login failed. Please try again.']);
    }
})->withoutMiddleware(['web']);

Route::post('/logout', function (Request $request) {
    return redirect('/')->withCookie(cookie()->forget('user_id'));
})->name('logout')->withoutMiddleware(['web']);

// PROTECTED ROUTES (with cookie-based authentication)
Route::get('/stocks', function (Request $request) {
    try {
        $userId = decrypt($request->cookie('user_id'));
        $user = User::find($userId);
        if (!$user) {
            return redirect('/login')->withErrors(['email' => 'Please log in to continue.']);
        }

        $stocks = $user->stocks;
        return view('stocks.index', compact('stocks'));
    } catch (Exception $e) {
        return redirect('/login')->withErrors(['email' => 'Please log in to continue.']);
    }
})->name('stocks.index')->withoutMiddleware(['web']);

Route::get('/stocks/create', function (Request $request) {
    try {
        $userId = decrypt($request->cookie('user_id'));
        $user = User::find($userId);
        if (!$user) {
            return redirect('/login');
        }
        return view('stocks.create');
    } catch (Exception $e) {
        return redirect('/login');
    }
})->name('stocks.create')->withoutMiddleware(['web']);

Route::post('/stocks', function (Request $request) {
    try {
        $userId = decrypt($request->cookie('user_id'));
        $user = User::find($userId);
        if (!$user) {
            return redirect('/login');
        }

        // Basic validation
        $symbol = $request->input('symbol');
        $name = $request->input('name');
        $price = $request->input('price');
        $change = $request->input('change');
        $trend = $request->input('trend');

        if (empty($symbol) || empty($name) || empty($price) || empty($change) || empty($trend)) {
            return back()->withErrors(['symbol' => 'All fields are required.']);
        }

        if (!is_numeric($price) || $price < 0) {
            return back()->withErrors(['price' => 'Price must be a positive number.']);
        }

        if (!is_numeric($change)) {
            return back()->withErrors(['change' => 'Change must be a number.']);
        }

        if (!in_array($trend, ['up', 'down', 'neutral'])) {
            return back()->withErrors(['trend' => 'Invalid trend value.']);
        }

        $user->stocks()->create([
            'symbol' => strtoupper($symbol),
            'name' => $name,
            'price' => $price,
            'change' => $change,
            'trend' => $trend,
        ]);

        return redirect()->route('stocks.index')->with('success', 'Stock added successfully!');
    } catch (Exception $e) {
        return back()->withErrors(['symbol' => 'Failed to add stock. Please try again.']);
    }
})->name('stocks.store')->withoutMiddleware(['web']);

Route::get('/stocks/{stock}', function (Request $request, $stockId) {
    try {
        $userId = decrypt($request->cookie('user_id'));
        $user = User::find($userId);
        if (!$user) {
            return redirect('/login');
        }

        $stock = $user->stocks()->findOrFail($stockId);
        return view('stocks.show', compact('stock'));
    } catch (Exception $e) {
        return redirect('/login');
    }
})->name('stocks.show')->withoutMiddleware(['web']);

Route::get('/stocks/{stock}/edit', function (Request $request, $stockId) {
    try {
        $userId = decrypt($request->cookie('user_id'));
        $user = User::find($userId);
        if (!$user) {
            return redirect('/login');
        }

        $stock = $user->stocks()->findOrFail($stockId);
        return view('stocks.edit', compact('stock'));
    } catch (Exception $e) {
        return redirect('/login');
    }
})->name('stocks.edit')->withoutMiddleware(['web']);

Route::put('/stocks/{stock}', function (Request $request, $stockId) {
    try {
        $userId = decrypt($request->cookie('user_id'));
        $user = User::find($userId);
        if (!$user) {
            return redirect('/login');
        }

        $stock = $user->stocks()->findOrFail($stockId);

        // Basic validation
        $symbol = $request->input('symbol');
        $name = $request->input('name');
        $price = $request->input('price');
        $change = $request->input('change');
        $trend = $request->input('trend');

        if (empty($symbol) || empty($name) || empty($price) || empty($change) || empty($trend)) {
            return back()->withErrors(['symbol' => 'All fields are required.']);
        }

        $stock->update([
            'symbol' => strtoupper($symbol),
            'name' => $name,
            'price' => $price,
            'change' => $change,
            'trend' => $trend,
        ]);

        return redirect()->route('stocks.index')->with('success', 'Stock updated successfully!');
    } catch (Exception $e) {
        return back()->withErrors(['symbol' => 'Failed to update stock. Please try again.']);
    }
})->name('stocks.update')->withoutMiddleware(['web']);

Route::delete('/stocks/{stock}', function (Request $request, $stockId) {
    try {
        $userId = decrypt($request->cookie('user_id'));
        $user = User::find($userId);
        if (!$user) {
            return redirect('/login');
        }

        $stock = $user->stocks()->findOrFail($stockId);
        $stock->delete();

        return redirect()->route('stocks.index')->with('success', 'Stock deleted successfully!');
    } catch (Exception $e) {
        return redirect()->route('stocks.index')->withErrors(['error' => 'Failed to delete stock.']);
    }
})->name('stocks.destroy')->withoutMiddleware(['web']);

// Test route for 500 error
Route::get('/test-500', function () {
    throw new Exception('Test exception for 500 error page');
});
