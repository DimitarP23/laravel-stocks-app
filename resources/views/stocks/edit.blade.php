@extends('layouts.app')

@section('title', 'Edit Stock')

@section('content')
<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card">
            <div class="card-header">
                <h3>✏️ Edit Stock: {{ $stock->symbol }}</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('stocks.update', $stock) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="symbol" class="form-label">Stock Symbol *</label>
                        <input type="text" class="form-control @error('symbol') is-invalid @enderror"
                               id="symbol" name="symbol" value="{{ old('symbol', $stock->symbol) }}"
                               placeholder="e.g., AAPL, MSFT, GOOGL" maxlength="10" required>
                        @error('symbol')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="name" class="form-label">Company Name *</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                               id="name" name="name" value="{{ old('name', $stock->name) }}"
                               placeholder="e.g., Apple Inc., Microsoft Corporation" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="price" class="form-label">Current Price ($) *</label>
                        <input type="number" step="0.01" min="0" class="form-control @error('price') is-invalid @enderror"
                               id="price" name="price" value="{{ old('price', $stock->price) }}"
                               placeholder="e.g., 173.50" required>
                        @error('price')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="change" class="form-label">Price Change</label>
                        <input type="text" class="form-control @error('change') is-invalid @enderror"
                               id="change" name="change" value="{{ old('change', $stock->change) }}"
                               placeholder="e.g., +2.5%, -1.2%">
                        @error('change')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="trend" class="form-label">Trend *</label>
                        <select class="form-select @error('trend') is-invalid @enderror" id="trend" name="trend" required>
                            <option value="">Select trend...</option>
                            <option value="up" {{ old('trend', $stock->trend) == 'up' ? 'selected' : '' }}>📈 Up</option>
                            <option value="down" {{ old('trend', $stock->trend) == 'down' ? 'selected' : '' }}>📉 Down</option>
                        </select>
                        @error('trend')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('stocks.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Back to Stocks
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Update Stock
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
