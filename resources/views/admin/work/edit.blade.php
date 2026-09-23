@extends('layouts.admin')

@section('title', 'Edit Project')
@section('page-title', 'Edit Project')

@section('content')
<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('admin.work.update', $project) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="row">
                <div class="col-md-6">
                    <x-input name="title_ar" label="Title (Arabic)" :value="$project->title_ar" required dir="rtl" />
                </div>
                <div class="col-md-6">
                    <x-input name="title_en" label="Title (English)" :value="$project->title_en" required />
                </div>
            </div>

            <div class="mb-3">
                <label for="description_ar" class="form-label">Description (Arabic) <span class="text-danger">*</span></label>
                <textarea id="description_ar" name="description_ar" rows="4" dir="rtl" class="form-control @error('description_ar') is-invalid @enderror">{{ old('description_ar', $project->description_ar) }}</textarea>
                @error('description_ar')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="description_en" class="form-label">Description (English) <span class="text-danger">*</span></label>
                <textarea id="description_en" name="description_en" rows="4" class="form-control @error('description_en') is-invalid @enderror">{{ old('description_en', $project->description_en) }}</textarea>
                @error('description_en')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            <x-input name="location" label="Location" :value="$project->location" required />

            @if ($project->galleryImages()->isNotEmpty())
                <div class="mb-3">
                    <label class="form-label d-block">Current Photos</label>
                    <div class="d-flex flex-wrap gap-3">
                        @foreach ($project->galleryImages() as $image)
                            <div class="text-center">
                                <img src="{{ $image->getUrl() }}" width="100" height="100" style="object-fit: cover;" class="rounded mb-1 d-block">
                                <div class="form-check form-check-inline">
                                    <input type="checkbox" class="form-check-input" name="remove_media[]" value="{{ $image->id }}" id="remove_media_{{ $image->id }}">
                                    <label class="form-check-label small text-danger" for="remove_media_{{ $image->id }}">Remove</label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="mb-3">
                <label for="photos" class="form-label">Add More Photos</label>
                <input type="file" id="photos" name="photos[]" multiple accept="image/*" class="form-control @error('photos') is-invalid @enderror">
                @error('photos')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">Update Project</button>
                <a href="{{ route('admin.work.index') }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
