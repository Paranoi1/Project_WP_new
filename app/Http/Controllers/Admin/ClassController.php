<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ClassController extends Controller
{
    public function index(): View
    {
        $courses = Course::orderBy('crs_ID', 'desc')->get();

        return view('admin.classes.index', compact('courses'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'crs_title' => ['required', 'string', 'max:255'],
            'crs_description' => ['nullable', 'string'],
        ]);

        Course::create($validated);

        return redirect()
            ->route('admin.classes.index')
            ->with('success', 'Course created successfully!');
    }
}