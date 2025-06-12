@extends('layouts.app')

@section('title', 'Hot Stocks')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>🔥 Hottest Stocks Available</h1>
            <a href="{{ route('stocks.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Add New Stock
            </a>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if($stocks->count() > 0)
            <div class="row">
                @foreach($stocks as $stock)
                    <div class="col-md-4 mb-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">{{ $stock->symbol }}</h5>
                                <h6 class="card-subtitle mb-2 text-muted">{{ $stock->name }}</h6>
                                <p class="card-text">
                                    <strong>Price:</strong> ${{ number_format($stock->price, 2) }}
                                    <br>
                                    @if($stock->change)
                                        <span class="text-{{ $stock->trend === 'up' ? 'success' : 'danger' }}">
                                            {{ $stock->change }}
                                            <i class="bi bi-arrow-{{ $stock->trend }}"></i>
                                        </span>
                                    @endif
                                </p>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('stocks.show', $stock) }}" class="btn btn-sm btn-outline-primary">View</a>
                                    <a href="{{ route('stocks.edit', $stock) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                                    <form action="{{ route('stocks.destroy', $stock) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger"
                                                onclick="return confirm('Are you sure you want to delete this stock?')">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="alert alert-info">
                <h4 class="alert-heading">📈 No Stocks Available</h4>
                <p>You haven't added any stocks yet. Click the "Add New Stock" button to get started!</p>
            </div>
        @endif

        <div class="alert alert-info mt-4">
            <h4 class="alert-heading">💡 Pro Tip!</h4>
            <p>Remember to diversify your portfolio and never invest more than you can afford to lose.</p>
        </div>
    </div>
</div>
@endsection
