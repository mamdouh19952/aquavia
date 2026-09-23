@extends('layouts.admin')

@section('title', 'Add Project')
@section('page-title', 'Add Project')

@section('content')
<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('admin.work.store') }}" enctype="multipart/form-data">
            @csrf

            <div class="row">
                <div class="col-md-6">
                    <x-input name="title_ar" label="Title (Arabic)" required dir="rtl" />
                </div>
                <div class="col-md-6">
                    <x-input name="title_en" label="Title (English)" required />
                </div>
            </div>

            <div class="mb-3">
                <label for="description_ar" class="form-label">Description (Arabic) <span class="text-danger">*</span></label>
                <textarea id="description_ar" name="description_ar" rows="4" dir="rtl" class="form-control @error('description_ar') is-invalid @enderror">{{ old('description_ar') }}</textarea>
                @error('description_ar')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="description_en" class="form-label">Description (English) <span class="text-danger">*</span></label>
                <textarea id="description_en" name="description_en" rows="4" class="form-control @error('description_en') is-invalid @enderror">{{ old('description_en') }}</textarea>
                @error('description_en')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            <x-input name="location" label="Location" required />

            <div class="mb-3">
                <label for="photos" class="form-label">Photos <span class="text-danger">*</span></label>
                <input type="file" id="photos" name="photos[]" multiple accept="image/*" class="form-control @error('photos') is-invalid @enderror">
                <div class="form-text">Select one or more photos for this project's gallery.</div>
                @error('photos')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">Save Project</button>
                <a href="{{ route('admin.work.index') }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
