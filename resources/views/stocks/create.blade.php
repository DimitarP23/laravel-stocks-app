@extends('layouts.app')

@section('title', 'Add New Stock')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Add New Stock</h5>
                    <a href="{{ route('stocks.index') }}" class="btn btn-secondary btn-sm">
                        <i class="bi bi-arrow-left"></i> Back to Stocks
                    </a>
                </div>
            </div>

            <div class="card-body">
                <form method="POST" action="{{ route('stocks.store') }}">

                    <div class="mb-3">
                        <label for="symbol" class="form-label">Stock Symbol</label>
                        <input id="symbol" type="text" class="form-control @error('symbol') is-invalid @enderror"
                               name="symbol" value="{{ old('symbol') }}" required maxlength="10"
                               placeholder="e.g., AAPL, GOOGL" style="text-transform: uppercase;">
                        @error('symbol')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Enter the stock ticker symbol (max 10 characters)</div>
                    </div>

                    <div class="mb-3">
                        <label for="name" class="form-label">Company Name</label>
                        <input id="name" type="text" class="form-control @error('name') is-invalid @enderror"
                               name="name" value="{{ old('name') }}" required maxlength="255"
                               placeholder="e.g., Apple Inc., Google LLC">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="price" class="form-label">Current Price ($)</label>
                        <input id="price" type="number" step="0.01" min="0"
                               class="form-control @error('price') is-invalid @enderror"
                               name="price" value="{{ old('price') }}" required
                               placeholder="e.g., 150.25">
                        @error('price')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="change" class="form-label">Price Change ($)</label>
                        <input id="change" type="number" step="0.01"
                               class="form-control @error('change') is-invalid @enderror"
                               name="change" value="{{ old('change') }}" required
                               placeholder="e.g., +2.50 or -1.25">
                        @error('change')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Enter positive values for gains, negative for losses</div>
                    </div>

                    <div class="mb-3">
                        <label for="trend" class="form-label">Trend</label>
                        <select id="trend" class="form-select @error('trend') is-invalid @enderror" name="trend" required>
                            <option value="">Select trend...</option>
                            <option value="up" {{ old('trend') == 'up' ? 'selected' : '' }}>📈 Up</option>
                            <option value="down" {{ old('trend') == 'down' ? 'selected' : '' }}>📉 Down</option>
                            <option value="neutral" {{ old('trend') == 'neutral' ? 'selected' : '' }}>➡️ Neutral</option>
                        </select>
                        @error('trend')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="{{ route('stocks.index') }}" class="btn btn-secondary me-md-2">Cancel</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-plus-circle"></i> Add Stock
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
