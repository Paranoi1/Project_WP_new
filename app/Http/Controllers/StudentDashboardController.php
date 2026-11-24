<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StudentDashboardController extends Controller
{
    public function index() {
        // В будущем здесь будет логика запросов к БД через модели Eloquent
        return view('student.dashboard');
    }
}