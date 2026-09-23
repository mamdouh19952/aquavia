<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreWorkProjectRequest;
use App\Http\Requests\Admin\UpdateWorkProjectRequest;
use App\Models\WorkProject;
use App\Services\MediaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class WorkController extends Controller
{
    public function __construct(private MediaService $mediaService)
    {
    }

    public function index(): View
    {
        $projects = WorkProject::latest()->get();

        return view('admin.work.index', compact('projects'));
    }

    public function create(): View
    {
        return view('admin.work.create');
    }

    public function store(StoreWorkProjectRequest $request): RedirectResponse
    {
        $project = WorkProject::create($request->safe()->except('photos'));

        foreach ($request->file('photos', []) as $photo) {
            $this->mediaService->upload($project, $photo, 'gallery');
        }

        return redirect()->route('admin.work.index')
            ->with('success', 'Project created successfully.');
    }

    public function edit(WorkProject $work): View
    {
        return view('admin.work.edit', ['project' => $work]);
    }

    public function update(UpdateWorkProjectRequest $request, WorkProject $work): RedirectResponse
    {
        $work->update($request->safe()->except(['photos', 'remove_media']));

        foreach ((array) $request->input('remove_media', []) as $mediaId) {
            $media = $work->media()->whereKey($mediaId)->first();

            if ($media instanceof Media) {
                $this->mediaService->deleteMediaItem($media);
            }
        }

        foreach ($request->file('photos', []) as $photo) {
            $this->mediaService->upload($work, $photo, 'gallery');
        }

        return redirect()->route('admin.work.index')
            ->with('success', 'Project updated successfully.');
    }

    public function destroy(WorkProject $work): RedirectResponse
    {
        $this->mediaService->deleteMedia($work, 'gallery');
        $work->delete();

        return redirect()->route('admin.work.index')
            ->with('success', 'Project deleted successfully.');
    }
}
