<?php

use Illuminate\Support\Facades\Route;
use App\Models\User;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cookie;

// DIAGNOSTIC TEST ROUTES - Testing different middleware combinations
Route::post('/test-form', function (Request $request) {
    return response()->json([
        'status' => 'success',
        'message' => 'Form submitted successfully!',
        'data' => $request->all()
    ]);
})->withoutMiddleware(['web']);

Route::get('/test-form', function () {
    return '
    <!DOCTYPE html>
    <html>
    <head><title>Test Form</title></head>
    <body>
        <h1>Test Form (No Middleware)</h1>
        <form method="POST" action="/test-form">
            <input type="text" name="test_field" placeholder="Enter anything" required>
            <button type="submit">Submit</button>
        </form>
    </body>
    </html>';
})->withoutMiddleware(['web']);

// COMPLETELY BYPASS WEB MIDDLEWARE GROUP
Route::post('/test-bypass-web', function (Request $request) {
    return response()->json([
        'status' => 'success',
        'message' => 'Bypassed web middleware completely!',
        'data' => $request->all()
    ]);
})->withoutMiddleware(['web'])->middleware([
    \App\Http\Middleware\DisableCsrf::class
]);

Route::get('/test-bypass-web', function () {
    return '
    <!DOCTYPE html>
    <html>
    <head><title>Bypass Web Middleware</title></head>
    <body>
        <h1>Bypass Web Middleware Test</h1>
        <p>This completely bypasses the web middleware group and manually adds DisableCsrf</p>
        <form method="POST" action="/test-bypass-web">
            <input type="text" name="test_field" placeholder="Enter anything" required>
            <button type="submit">Submit</button>
        </form>
    </body>
    </html>';
})->withoutMiddleware(['web']);

// ULTRA MINIMAL - Only our DisableCsrf middleware
Route::post('/test-ultra-minimal', function (Request $request) {
    return response()->json([
        'status' => 'success',
        'message' => 'Ultra minimal test successful!',
        'data' => $request->all()
    ]);
})->middleware([\App\Http\Middleware\DisableCsrf::class]);

Route::get('/test-ultra-minimal', function () {
    return '
    <!DOCTYPE html>
    <html>
    <head><title>Ultra Minimal Test</title></head>
    <body>
        <h1>Ultra Minimal Test (Only DisableCsrf)</h1>
        <form method="POST" action="/test-ultra-minimal">
            <input type="text" name="test_field" placeholder="Enter anything" required>
            <button type="submit">Submit</button>
        </form>
    </body>
    </html>';
});

// Test with only essential middleware (no sessions)
Route::post('/test-minimal', function (Request $request) {
    return response()->json([
        'status' => 'success',
        'message' => 'Minimal middleware test successful!',
        'data' => $request->all()
    ]);
})->middleware([\App\Http\Middleware\EncryptCookies::class, \App\Http\Middleware\DisableCsrf::class]);

Route::get('/test-minimal', function () {
    return '
    <!DOCTYPE html>
    <html>
    <head><title>Minimal Middleware Test</title></head>
    <body>
        <h1>Minimal Middleware Test</h1>
        <form method="POST" action="/test-minimal">
            <input type="text" name="test_field" placeholder="Enter anything" required>
            <button type="submit">Submit</button>
        </form>
    </body>
    </html>';
});

// Test with sessions but no CSRF
Route::post('/test-sessions', function (Request $request) {
    return response()->json([
        'status' => 'success',
        'message' => 'Sessions test successful!',
        'data' => $request->all(),
        'session_id' => session()->getId()
    ]);
})->middleware([
    \App\Http\Middleware\EncryptCookies::class,
    \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
    \Illuminate\Session\Middleware\StartSession::class,
    \App\Http\Middleware\DisableCsrf::class
]);

Route::get('/test-sessions', function () {
    return '
    <!DOCTYPE html>
    <html>
    <head><title>Sessions Test</title></head>
    <body>
        <h1>Sessions Test</h1>
        <p>Session ID: ' . session()->getId() . '</p>
        <form method="POST" action="/test-sessions">
            <input type="text" name="test_field" placeholder="Enter anything" required>
            <button type="submit">Submit</button>
        </form>
    </body>
    </html>';
});

// Test registration with minimal middleware (bypassing ShareErrorsFromSession)
Route::post('/test-register', function (Request $request) {
    try {
        $name = $request->input('name');
        $email = $request->input('email');
        $password = $request->input('password');

        if (empty($name) || empty($email) || empty($password)) {
            return response()->json(['error' => 'All fields are required.'], 400);
        }

        if (strlen($password) < 8) {
            return response()->json(['error' => 'Password must be at least 8 characters.'], 400);
        }

        if (User::where('email', $email)->exists()) {
            return response()->json(['error' => 'Email already exists.'], 400);
        }

        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Registration successful!',
            'user_id' => $user->id
        ]);
    } catch (Exception $e) {
        return response()->json(['error' => 'Registration failed: ' . $e->getMessage()], 500);
    }
})->middleware([
    \App\Http\Middleware\EncryptCookies::class,
    \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
    \Illuminate\Session\Middleware\StartSession::class,
    \App\Http\Middleware\DisableCsrf::class,
    \Illuminate\Routing\Middleware\SubstituteBindings::class
]);

Route::get('/test-register', function () {
    return '
    <!DOCTYPE html>
    <html>
    <head><title>Test Registration</title></head>
    <body>
        <h1>Test Registration (Minimal Middleware)</h1>
        <form method="POST" action="/test-register">
            <div>
                <label>Name:</label>
                <input type="text" name="name" required>
            </div>
            <div>
                <label>Email:</label>
                <input type="email" name="email" required>
            </div>
            <div>
                <label>Password:</label>
                <input type="password" name="password" required>
            </div>
            <button type="submit">Register</button>
        </form>
    </body>
    </html>';
});

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

// AUTHENTICATION ROUTES (using custom DisableCsrf middleware)
Route::get('/register', function () {
    return view('auth.register');
})->name('register');

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
});

Route::get('/login', function () {
    return view('auth.login');
})->name('login');

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
});

Route::post('/logout', function (Request $request) {
    return redirect('/')->withCookie(cookie()->forget('user_id'));
})->name('logout');

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
})->name('stocks.index');

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
})->name('stocks.create');

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
})->name('stocks.store');

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
})->name('stocks.show');

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
})->name('stocks.edit');

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
})->name('stocks.update');

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
})->name('stocks.destroy');

// Test route for 500 error
Route::get('/test-500', function () {
    throw new Exception('Test exception for 500 error page');
});
