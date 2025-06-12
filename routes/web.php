<?php

use Illuminate\Support\Facades\Route;
use App\Models\User;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;


Route::get('/', function () {
    return view('home');
});

Route::get('/about', function () {
    return view('about');
});

// Test route for 500 error
Route::get('/test-500', function () {
    throw new Exception('Test exception for 500 error page');
});

// STATELESS AUTHENTICATION (no sessions, using cookies)
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::post('/login', function (Request $request) {
    try {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if ($user && Hash::check($credentials['password'], $user->password)) {
            // Store user ID in encrypted cookie instead of session
            $cookie = cookie('user_id', encrypt($user->id), 60 * 24 * 7); // 7 days
            return redirect('/stocks')->cookie($cookie);
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    } catch (Exception $e) {
        return back()->withErrors(['email' => 'Login failed. Please try again.']);
    }
});

Route::get('/register', function () {
    return view('auth.register');
})->name('register');

Route::post('/register', function (Request $request) {
    try {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:10|confirmed',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        // Store user ID in encrypted cookie instead of session
        $cookie = cookie('user_id', encrypt($user->id), 60 * 24 * 7); // 7 days
        return redirect('/stocks')->with('success', 'Registration successful!')->cookie($cookie);
    } catch (Exception $e) {
        return back()->withErrors(['email' => 'Registration failed. Please try again.']);
    }
});

// Custom auth middleware using cookies
Route::middleware(['web'])->group(function () {
    Route::post('/logout', function (Request $request) {
        return redirect('/')->withCookie(cookie()->forget('user_id'));
    })->name('logout');

    // Protected routes with cookie-based auth
    Route::get('/stocks', function (Request $request) {
        try {
            $userId = decrypt($request->cookie('user_id'));
            $user = User::find($userId);
            if (!$user) {
                return redirect('/login');
            }

            $stocks = $user->stocks;
            return view('stocks.index', compact('stocks'));
        } catch (Exception $e) {
            return redirect('/login');
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

            $validated = $request->validate([
                'symbol' => 'required|string|max:10',
                'name' => 'required|string|max:255',
                'price' => 'required|numeric|min:0',
                'change' => 'required|numeric',
                'trend' => 'required|in:up,down,neutral',
            ]);

            $user->stocks()->create($validated);
            return redirect()->route('stocks.index')->with('success', 'Stock added successfully!');
        } catch (Exception $e) {
            return redirect('/login');
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

            $validated = $request->validate([
                'symbol' => 'required|string|max:10',
                'name' => 'required|string|max:255',
                'price' => 'required|numeric|min:0',
                'change' => 'required|numeric',
                'trend' => 'required|in:up,down,neutral',
            ]);

            $stock->update($validated);
            return redirect()->route('stocks.index')->with('success', 'Stock updated successfully!');
        } catch (Exception $e) {
            return redirect('/login');
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
            return redirect('/login');
        }
    })->name('stocks.destroy');
});
