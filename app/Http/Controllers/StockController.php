<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Stock;
use Illuminate\Support\Facades\Auth;

class StockController extends Controller
{
    public function index()
    {
        // IDOR Protection: Only show stocks belonging to the authenticated user
        $stocks = Auth::user()->stocks;
        return view('stocks.index', compact('stocks'));
    }

    public function create()
    {
        return view('stocks.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'symbol' => 'required|string|max:10|unique:stocks',
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'change' => 'nullable|string|max:20',
            'trend' => 'required|in:up,down'
        ]);

        // IDOR Protection: Automatically assign to authenticated user
        Auth::user()->stocks()->create($request->all());

        return redirect()->route('stocks.index')->with('success', 'Stock added successfully!');
    }

    public function show(Stock $stock)
    {
        // IDOR Protection: Ensure user can only view their own stocks
        if ($stock->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this stock.');
        }

        return view('stocks.show', compact('stock'));
    }

    public function edit(Stock $stock)
    {
        // IDOR Protection: Ensure user can only edit their own stocks
        if ($stock->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this stock.');
        }

        return view('stocks.edit', compact('stock'));
    }

    public function update(Request $request, Stock $stock)
    {
        // IDOR Protection: Ensure user can only update their own stocks
        if ($stock->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this stock.');
        }

        $request->validate([
            'symbol' => 'required|string|max:10|unique:stocks,symbol,' . $stock->id,
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'change' => 'nullable|string|max:20',
            'trend' => 'required|in:up,down'
        ]);

        $stock->update($request->all());

        return redirect()->route('stocks.index')->with('success', 'Stock updated successfully!');
    }

    public function destroy(Stock $stock)
    {
        // IDOR Protection: Ensure user can only delete their own stocks
        if ($stock->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this stock.');
        }

        $stock->delete();
        return redirect()->route('stocks.index')->with('success', 'Stock deleted successfully!');
    }
}
