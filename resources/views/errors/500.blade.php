@extends('layouts.app')

@section('title', '500 - Server Error')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-8 text-center">
            <h1 class="display-1">500</h1>
            <h2 class="mb-4">Server Error</h2>
            <div class="card">
                <div class="card-body">
                    <p class="card-text">Oops! Something went wrong on our servers.</p>
                    <p class="card-text">We're working to fix this issue. Please try again later.</p>
                    <div class="mt-4">
                        <a href="/" class="btn btn-primary">Back to Home</a>
                        <button onclick="handleRetry()" class="btn btn-secondary ms-2">Try Again</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Check if retry count exists in session storage
        let retryCount = sessionStorage.getItem('errorRetryCount') || 0;

        function handleRetry() {
            retryCount++;
            sessionStorage.setItem('errorRetryCount', retryCount);

            if (retryCount >= 2) {
                // After second retry, redirect to support
                window.location.href = '/contact';
            } else {
                // Otherwise, reload the page
                location.reload();
            }
        }
    </script>
@endsection
