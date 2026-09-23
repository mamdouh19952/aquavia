<?php

namespace App\Http\Controllers;

use App\Models\WorkProject;
use Illuminate\Http\Response;
use Illuminate\View\View;

class WorkController extends Controller
{
    public function index(): View
    {
        $projects = WorkProject::latest()->get();

        return view('public.work.index', compact('projects'));
    }

    public function show(int $workProject): View|Response
    {
        $project = WorkProject::find($workProject);

        if (! $project) {
            return response()->view('public.work.not-found', [], 404);
        }

        return view('public.work.show', ['project' => $project]);
    }
}
