@extends('layouts.app')

@section('title', $stock->symbol . ' - Stock Details')

@section('content')
<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card">
            <div class="card-header">
                <h3>📊 Stock Details: {{ $stock->symbol }}</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h4>{{ $stock->name }}</h4>
                        <p class="text-muted">{{ $stock->symbol }}</p>
                    </div>
                    <div class="col-md-6 text-end">
                        <h2 class="text-primary">${{ number_format($stock->price, 2) }}</h2>
                        @if($stock->change)
                            <p class="text-{{ $stock->trend === 'up' ? 'success' : 'danger' }} fs-5">
                                {{ $stock->change }}
                                <i class="bi bi-arrow-{{ $stock->trend }}"></i>
                            </p>
                        @endif
                    </div>
                </div>

                <hr>

                <div class="row">
                    <div class="col-md-6">
                        <strong>Company Name:</strong><br>
                        {{ $stock->name }}
                    </div>
                    <div class="col-md-6">
                        <strong>Stock Symbol:</strong><br>
                        {{ $stock->symbol }}
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-6">
                        <strong>Current Price:</strong><br>
                        ${{ number_format($stock->price, 2) }}
                    </div>
                    <div class="col-md-6">
                        <strong>Price Change:</strong><br>
                        @if($stock->change)
                            <span class="text-{{ $stock->trend === 'up' ? 'success' : 'danger' }}">
                                {{ $stock->change }}
                            </span>
                        @else
                            <span class="text-muted">No change data</span>
                        @endif
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-6">
                        <strong>Trend:</strong><br>
                        <span class="text-{{ $stock->trend === 'up' ? 'success' : 'danger' }}">
                            @if($stock->trend === 'up')
                                📈 Upward
                            @else
                                📉 Downward
                            @endif
                        </span>
                    </div>
                    <div class="col-md-6">
                        <strong>Last Updated:</strong><br>
                        {{ $stock->updated_at->format('M d, Y \a\t g:i A') }}
                    </div>
                </div>

                <hr>

                <div class="d-flex justify-content-between">
                    <a href="{{ route('stocks.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Back to Stocks
                    </a>
                    <div>
                        <a href="{{ route('stocks.edit', $stock) }}" class="btn btn-primary me-2">
                            <i class="bi bi-pencil"></i> Edit Stock
                        </a>
                        <form action="{{ route('stocks.destroy', $stock) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger"
                                    onclick="return confirm('Are you sure you want to delete this stock?')">
                                <i class="bi bi-trash"></i> Delete Stock
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
