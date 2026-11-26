<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(): View
    {   

        $user = User::with([
            'role',
            'courses.modules.assignments',
            'courses.modules.lectures',
            'courses.forum.publications.user',
            'studentassignments.submissions',
        ])->findOrFail(Auth::id());

        $courses = $user->courses;

        // Индексируем записи о сдаче заданий по ID задания (sgn_ID) для быстрого поиска статуса
        $studentAssignmentsMap = $user->studentassignments->keyBy('stSgn_assignment');

        $allAssignments = collect();
        $calendarItems = collect();
        $eventsFeed = collect();
        $coursesPayload = [];

        foreach ($courses as $course) {
            $courseAssignments = [];
            $courseLectures = [];
            $coursePublications = [];

            // 1. Собираем лекции и задания по всем модулям курса
            foreach ($course->modules as $module) {
                foreach ($module->lectures as $lecture) {
                    $lectureDate = $lecture->lct_date ? $lecture->lct_date->format('d.m.Y') : 'Без даты';

                    $courseLectures[] = [
                        'id'          => $lecture->lct_ID,
                        'module'      => $module->mod_title,
                        'title'       => $lecture->lct_title,
                        'description' => $lecture->lct_description,
                        'date'        => $lectureDate,
                    ];

                    if ($lecture->lct_date) {
                        $calendarItems->push([
                            'sort_date' => $lecture->lct_date->format('Y-m-d'),
                            'date'      => $lectureDate,
                            'text'      => "Лекция: {$lecture->lct_title} ({$course->crs_title})",
                        ]);
                    }
                }

                foreach ($module->assignments as $assignment) {
                    $stAssignment = $studentAssignmentsMap->get($assignment->sgn_ID);
                    $latestSubmission = $stAssignment?->submissions->sortByDesc('sbm_ID')->first();

                    $status = $latestSubmission ? 'submitted' : 'not_submitted';
                    $deadlineFormatted = $assignment->sgn_deadline
                        ? $assignment->sgn_deadline->format('d.m.Y')
                        : 'Без срока';

                    $assignmentData = [
                        'id'           => $assignment->sgn_ID,
                        'course_id'    => $course->crs_ID,
                        'course_title' => $course->crs_title,
                        'module'       => $module->mod_title,
                        'desc'         => $assignment->sgn_desc,
                        'deadline'     => $deadlineFormatted,
                        'max_score'    => $assignment->sgn_maxScore,
                        'status'       => $status,
                        'score'        => $latestSubmission?->sbm_score,
                        'feedback'     => $latestSubmission?->sbm_feedback,
                    ];

                    $courseAssignments[] = $assignmentData;
                    $allAssignments->push($assignmentData);

                    if ($assignment->sgn_deadline) {
                        $calendarItems->push([
                            'sort_date' => $assignment->sgn_deadline->format('Y-m-d'),
                            'date'      => $deadlineFormatted,
                            'text'      => "Дедлайн: {$assignment->sgn_desc} ({$course->crs_title})",
                        ]);
                    }

                    $eventsFeed->push([
                        'type'          => 'assignment',
                        'text'          => "Задание по курсу «{$course->crs_title}»: {$assignment->sgn_desc}",
                        'button_text'   => 'Перейти к заданию',
                        'course_id'     => $course->crs_ID,
                        'target_tab'    => 'course-assignments',
                        'assignment_id' => $assignment->sgn_ID,
                    ]);
                }
            }

            // 2. Собираем публикации из форума курса
            if ($course->forum && $course->forum->publications) {
                foreach ($course->forum->publications as $pub) {
                    $coursePublications[] = [
                        'id'      => $pub->pbl_ID,
                        'title'   => $pub->pbl_title,
                        'content' => $pub->pbl_content,
                        'author'  => $pub->user->name ?? 'Преподаватель',
                    ];

                    $eventsFeed->push([
                        'type'          => 'publication',
                        'text'          => "Новое сообщение в «{$course->crs_title}»: {$pub->pbl_title}",
                        'button_text'   => 'Прочитать сообщение',
                        'course_id'     => $course->crs_ID,
                        'target_tab'    => 'course-publications',
                        'assignment_id' => null,
                    ]);
                }
            }

            $coursesPayload[$course->crs_ID] = [
                'id'           => $course->crs_ID,
                'title'        => $course->crs_title,
                'description'  => $course->crs_description,
                'publications' => $coursePublications,
                'lectures'     => $courseLectures,
                'assignments'  => $courseAssignments,
            ];
        }

        $calendarItems = $calendarItems->sortBy('sort_date')->values();

        return view('dashboard', compact(
            'user',
            'courses',
            'allAssignments',
            'calendarItems',
            'eventsFeed',
            'coursesPayload'
        ));
    }
}