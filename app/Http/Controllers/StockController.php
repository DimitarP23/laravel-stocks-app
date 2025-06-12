<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Stock;

class StockController extends Controller
{
    public function index()
    {
        $stocks = Stock::all();
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

        Stock::create($request->all());

        return redirect()->route('stocks.index')->with('success', 'Stock added successfully!');
    }

    public function show(Stock $stock)
    {
        return view('stocks.show', compact('stock'));
    }

    public function edit(Stock $stock)
    {
        return view('stocks.edit', compact('stock'));
    }

    public function update(Request $request, Stock $stock)
    {
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
        $stock->delete();
        return redirect()->route('stocks.index')->with('success', 'Stock deleted successfully!');
    }
}
