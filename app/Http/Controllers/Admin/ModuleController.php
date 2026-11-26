<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ModuleController extends Controller
{
    public function index($courseId): View
    {
        $course = Course::with('modules')->findOrFail($courseId);

        return view('admin.modules.index', compact('course'));
    }

    public function store(Request $request, $courseId): RedirectResponse
    {
        $course = Course::findOrFail($courseId);

        $validated = $request->validate([
            'mod_title' => ['required', 'string', 'max:30'],
        ]);

        $course->modules()->create([
            'mod_title' => $validated['mod_title'],
        ]);

        return redirect()
            ->route('admin.classes.modules.index', $course->crs_ID)
            ->with('success', 'Module created successfully!');
    }
}