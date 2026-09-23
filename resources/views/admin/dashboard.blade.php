@extends('layouts.admin')

@section('title', 'Admin Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="row">
    <div class="col-md-4 mb-4">
        <div class="card">
            <div class="card-body">
                <h6 class="text-muted">Total Users</h6>
                <h2 class="mb-0">{{ \App\Models\User::count() }}</h2>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <h5 class="card-title">Welcome, {{ auth()->user()->name }}</h5>
        <p class="text-muted mb-0">This is your admin dashboard. Add your management sections here.</p>
    </div>
</div>
@endsection
