<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\View\View;

class CourseController extends Controller
{
    /**
     * Поиск курсов текущего пользователя (ID = 1) и передача их в представление.
     */
    public function myCourses(): View
    {
        $currentUserId = 1;

        // Находим пользователя с ID = 1 и сразу подгружаем его курсы (Eager Loading)
        $user = User::with('courses')->findOrFail($currentUserId);
        $courses = $user->courses;

        return view('courses.index', compact('user', 'courses'));
    }
}