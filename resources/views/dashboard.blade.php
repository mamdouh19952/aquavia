@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h1 class="card-title">Dashboard</h1>
                <p class="card-text text-muted">Welcome, {{ Auth::user()->name }}!</p>

                <p>
                    This is your Laravel Blade project with:
                </p>
                <ul>
                    <li>✅ Laravel 12</li>
                    <li>✅ Bootstrap 5</li>
                    <li>✅ Spatie Media Library (with MediaService wrapper)</li>
                    <li>✅ Clean CLAUDE.md with best practices</li>
                    <li>✅ Blade components framework</li>
                    <li>✅ Toastr notifications</li>
                </ul>

                <p class="mt-4">
                    Start building your features by following the patterns in CLAUDE.md.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
