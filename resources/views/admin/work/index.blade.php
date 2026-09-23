@extends('layouts.admin')

@section('title', 'Our Work')
@section('page-title', 'Our Work')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="mb-0">Our Work Projects</h5>
    <a href="{{ route('admin.work.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg"></i> Add Project
    </a>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>Photo</th>
                    <th>Title (AR)</th>
                    <th>Title (EN)</th>
                    <th>Location</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($projects as $project)
                    <tr>
                        <td>
                            @if ($project->thumbnail())
                                <img src="{{ $project->thumbnail()->getUrl() }}" alt="{{ $project->title_en }}" width="60" height="60" style="object-fit: cover;" class="rounded">
                            @endif
                        </td>
                        <td>{{ $project->title_ar }}</td>
                        <td>{{ $project->title_en }}</td>
                        <td>{{ $project->location }}</td>
                        <td class="text-end">
                            <a href="{{ route('admin.work.edit', $project) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                            <form method="POST" action="{{ route('admin.work.destroy', $project) }}" class="d-inline" onsubmit="return confirm('Delete this project?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">No projects yet — add your first one.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
