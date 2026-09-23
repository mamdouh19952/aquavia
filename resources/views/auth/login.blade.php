@extends('layouts.guest')

@section('title', 'Login')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-5">
        <div class="card auth-card shadow-sm">
            <div class="card-body p-4">
                <h3 class="auth-brand text-center mb-1">{{ config('app.name') }}</h3>
                <p class="text-muted text-center small mb-4">{{ __('site.tagline') }}</p>

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <x-input name="email" type="email" label="Email" required autofocus />
                    <x-input name="password" type="password" label="Password" required />

                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="remember" name="remember">
                        <label class="form-check-label" for="remember">Remember me</label>
                    </div>

                    <x-button type="submit" class="w-100">Login</x-button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
