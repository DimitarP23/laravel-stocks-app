@extends('layouts.app')

@section('title', 'Contact Us')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <h1 class="mb-4">Contact Us</h1>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card">
            <div class="card-body">
                <form action="/contact" method="POST" novalidate>
                    @csrf

                    <div class="mb-3">
                        <label for="name" class="form-label">Name *</label>
                        <input type="text"
                               class="form-control @error('name') is-invalid @enderror"
                               id="name"
                               name="name"
                               value="{{ old('name') }}"
                               required
                               minlength="10"
                               pattern="[A-Za-z\s]+"
                               oninput="this.value = this.value.replace(/[^A-Za-z\s]/g, '')"
                               title="Please enter your full name (minimum 10 characters, letters only)"
                               placeholder="Enter your full name (e.g., John Smith)">
                        @error('name')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                        <div class="form-text text-muted">
                            <ul class="mb-0">

                                <li>Enter your full name(letters and spaces only)</li>

                            </ul>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email *</label>
                        <input type="email"
                               class="form-control @error('email') is-invalid @enderror"
                               id="email"
                               name="email"
                               value="{{ old('email') }}"
                               required
                               pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$">
                        @error('email')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                        <div class="form-text">We'll never share your email with anyone else</div>
                    </div>

                    <div class="mb-3">
                        <label for="subject" class="form-label">Subject *</label>
                        <select class="form-select @error('subject') is-invalid @enderror"
                                id="subject"
                                name="subject"
                                required>
                            <option value="" selected disabled>Choose a subject...</option>
                            <option value="general" {{ old('subject') == 'general' ? 'selected' : '' }}>General Inquiry</option>
                            <option value="support" {{ old('subject') == 'support' ? 'selected' : '' }}>Technical Support</option>
                            <option value="feedback" {{ old('subject') == 'feedback' ? 'selected' : '' }}>Feedback</option>
                        </select>
                        @error('subject')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="message" class="form-label">Message *</label>
                        <textarea class="form-control @error('message') is-invalid @enderror"
                                  id="message"
                                  name="message"
                                  rows="5"
                                  required
                                  minlength="10">{{ old('message') }}</textarea>
                        @error('message')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                        <div class="form-text">Minimum 10 characters</div>
                    </div>

                    <button type="submit" class="btn btn-primary">Send Message</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Real-time character count and validation feedback
document.getElementById('name').addEventListener('input', function() {
    const nameInput = this;
    const currentLength = nameInput.value.length;
    const minLength = 10;
    const feedback = nameInput.nextElementSibling.nextElementSibling;

    if (currentLength < minLength) {
        feedback.innerHTML = `
            <ul class="mb-0 text-warning">
                <li>Minimum 10 characters (${currentLength}/${minLength} entered)</li>
                <li>Letters and spaces only</li>
                <li>Numbers and special characters not allowed</li>
            </ul>
        `;
    } else {
        feedback.innerHTML = `
            <ul class="mb-0 text-success">
                <li>✓ Length requirement met (${currentLength} characters)</li>
                <li>✓ Using valid characters</li>
            </ul>
        `;
    }
});
</script>
@endsection
