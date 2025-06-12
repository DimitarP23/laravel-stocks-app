@extends('layouts.app')

@section('title', 'Home')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <h1 class="mb-4">Welcome to MyWallet</h1>
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Getting Started</h5>
                    <p class="card-text">This is the home page of our application. Feel free to explore!</p>
                    <a href="/about" class="btn btn-primary">Learn More</a>
                </div>
            </div>
        </div>
    </div>
@endsection
