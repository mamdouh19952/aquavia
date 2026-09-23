@extends('layouts.app')

@section('title', 'Profile')

@section('content')
<div class="row">
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title mb-3">Profile Information</h5>

                <form method="POST" action="{{ route('profile.update') }}">
                    @csrf
                    @method('PUT')

                    <x-input name="name" label="Name" :value="$user->name" required />
                    <x-input name="email" type="email" label="Email" :value="$user->email" required />

                    <x-button type="submit">Save Changes</x-button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title mb-3">Update Password</h5>

                <form method="POST" action="{{ route('profile.password') }}">
                    @csrf
                    @method('PUT')

                    <x-input name="current_password" type="password" label="Current Password" required />
                    <x-input name="password" type="password" label="New Password" required />
                    <x-input name="password_confirmation" type="password" label="Confirm New Password" required />

                    <x-button type="submit">Update Password</x-button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
