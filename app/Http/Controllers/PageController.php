<?php

namespace App\Http\Controllers;

use App\Models\WorkProject;
use Illuminate\View\View;

class PageController extends Controller
{
    public function home(): View
    {
        $featuredProjects = WorkProject::latest()->take(3)->get();

        return view('public.home', compact('featuredProjects'));
    }

    public function about(): View
    {
        return view('public.about');
    }

    public function services(): View
    {
        return view('public.services');
    }

    public function contact(): View
    {
        return view('public.contact');
    }
}
