@extends('layouts.app')

@section('title', 'Too Many Attempts')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">Too Many Attempts</div>

            <div class="card-body">
                <div class="alert alert-warning">
                    <h4 class="alert-heading"><i class="bi bi-exclamation-triangle"></i> Please Slow Down</h4>
                    <p>You've made too many login attempts. Please wait 1 minute before trying again.</p>
                </div>

                <a href="{{ route('login') }}" class="btn btn-primary">Return to Login</a>
            </div>
        </div>
    </div>
</div>
@endsection
