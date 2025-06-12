@extends('layouts.app')

@section('title', '404 - Page Not Found')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-8 text-center">
            <h1 class="display-1">404</h1>
            <h2 class="mb-4">Page Not Found</h2>
            <div class="card">
                <div class="card-body">
                    <p class="card-text">Oops! The page you're looking for doesn't exist.</p>
                    <p class="card-text">You might have mistyped the address or the page may have moved.</p>
                    <div class="mt-4">
                        <a href="/" class="btn btn-primary">Back to Home</a>
                        <button onclick="history.back()" class="btn btn-secondary ms-2">Go Back</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
