<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>LMS</title>
</head>
<body>

    <!-- Навигация (Сайдбар) -->
    <nav>
        <h2>Navigation</h2>
        <ul>
            <li><button onclick="switchTab('dashboard')">Main</button></li>
            <li><button onclick="switchTab('courses')">Courses ({{ $courses->count() }})</button></li>
            <li><button onclick="switchTab('assignments')">Assignments ({{ $allAssignments->count() }})</button></li>
            <li><button onclick="switchTab('calendar')">Calendar</button></li>
            
                @if (auth()->user()->role?->rl_name === 'Admin')
                    <li>
                        <a href="{{ route('admin.classes.index') }}" class="admin-btn">
                            Manage Classes
                        </a>
                    </li>
                @endif

            <!-- <li><button onclick="switchTab('events')">События ({{ $eventsFeed->count() }})</button></li> -->
        </ul>
        <hr>
        <button onclick="toggleProfile()">Profile ({{ $user->name }})</button>
        
        <div id="profile-menu" style="display: none;">
            <p>{{ $user->name }} (ID: {{ $user->id }})</p>
            <p>{{ $user->email }}</p>
            <p>Роль: {{ $user->role->rl_name }}</p>

            <form method="POST" action="{{ route('logout') }}">
                @csrf

                <button type="submit" class="logout-btn">
                    Log out
                </button>
            </form>
        </div>
    </nav>

    <hr>

    <!-- Основной контент -->
    <main>
        <!-- Главная -->
        <section id="tab-dashboard">
            
            <ul>
                <li>Total Courses: <strong>{{ $courses->count() }}</strong></li>
                <li>Total Assignments: <strong>{{ $allAssignments->count() }}</strong></li>
                <li>Not Submitted Assignments: <strong>{{ $allAssignments->where('status', 'not_submitted')->count() }}</strong></li>
            </ul>
        </section>

        <!-- Список курсов -->
        <section id="tab-courses" style="display: none;">
            <h1>My courses</h1>
            <ul>
                @forelse($courses as $course)
                    <li>
                        <button onclick="openCourse({{ $course->crs_ID }})">
                            {{ $course->crs_title }}
                        </button>
                        @if($course->pivot && $course->pivot->enr_date)
                            <small>(Enrollment Date: {{ \Carbon\Carbon::parse($course->pivot->enr_date)->format('d.m.Y') }})</small>
                        @endif
                    </li>
                @empty
                    <li>You are not enrolled in any courses yet.</li>
                @endforelse
            </ul>
        </section>

        <!-- Внутренняя страница курса (заполняется динамически из JS) -->
        <section id="course-details" style="display: none;">
            <button onclick="switchTab('courses')">← Back to course list</button>
            <h1 id="course-title">Course Title</h1>
            <p id="course-description" style="color: #555;"></p>
            
            <nav>
                <button onclick="switchCourseTab('course-publications')">Publications</button>
                <button onclick="switchCourseTab('course-materials')">Materials (Lectures)</button>
                <button onclick="switchCourseTab('course-assignments')">Assignments</button>
            </nav>
            <br>

            <div id="course-publications">
                <h3>Publications</h3>
                <div id="course-publications-list"></div>
            </div>
            
            <div id="course-materials" style="display: none;">
                <h3>Materials (Lectures)</h3>
                <ul id="course-materials-list"></ul>
            </div>
            
            <div id="course-assignments" style="display: none;">
                <h3>Assignments</h3>
                <ul id="course-assignments-list"></ul>
            </div>
        </section>

        <!-- Все задания -->
        <section id="tab-assignments" style="display: none;">
            <h1>Assignments</h1>
            <ul>
                @forelse($allAssignments as $item)
                    <li>
                        <strong>{{ $item['course_title'] }}</strong> ({{ $item['module'] }}):
                        {{ $item['desc'] }} — Срок: {{ $item['deadline'] }}
                        [{{ $item['status'] === 'submitted' ? 'Сдано' : 'Не сдано' }}]
                        <button onclick="openAssignmentModalById({{ $item['id'] }})">Просмотр</button>
                    </li>
                @empty
                    <li>No assignments available.</li>
                @endforelse
            </ul>
        </section>

        <!-- Календарь -->
        <section id="tab-calendar" style="display: none;">
            <h1>Calendar</h1>
            <ul>
                @forelse($calendarItems as $cal)
                    <li><strong>{{ $cal['date'] }}</strong> — {{ $cal['text'] }}</li>
                @empty
                    <li>No scheduled lectures or deadlines in the calendar.</li>
                @endforelse
            </ul>
        </section>

        <!-- События (Уведомления) -->
        <section id="tab-events" style="display: none;">
            <h1>Events Feed</h1>
            <ul>
                @forelse($eventsFeed as $event)
                    <li>
                        {{ $event['text'] }}
                        <button onclick="handleEventClick({{ $event['course_id'] }}, '{{ $event['target_tab'] }}', {{ $event['assignment_id'] ?? 'null' }})">
                            {{ $event['button_text'] }}
                        </button>
                    </li>
                @empty
                    <li>No new events.</li>
                @endforelse
            </ul>
        </section>
    </main>

    <!-- Модальные окна -->
    <dialog id="assignment-modal">
        <h2 id="modal-title">Assignment</h2>
        <p><strong>Course:</strong> <span id="modal-course"></span></p>
        <p><strong>Deadline:</strong> <span id="modal-deadline"></span></p>
        <p><strong>Max Points:</strong> <span id="modal-max-score"></span></p>
        <p><strong>Status:</strong> <span id="modal-status"></span></p>
        <p id="modal-feedback-row" style="display: none;"><strong>Comment:</strong> <span id="modal-feedback"></span></p>
        
        <div id="modal-upload-section">
            <label>Attach File:</label>
            <input type="file">
            <button onclick="alert('File uploaded!')">Upload</button>
            <br><br>
            <button onclick="alert('Assignment submitted for review!')">Submit Work</button>
        </div>
        
        <br>
        <button onclick="document.getElementById('assignment-modal').close()">Close Window</button>
    </dialog>

    <!-- Скрипты логики -->
    <script>
        // Все данные по курсам, лекциям, публикациям и заданиям текущего пользователя из БД
        const coursesData = @json($coursesPayload);
        const allAssignmentsList = @json($allAssignments->values());

        function escapeHtml(text) {
            if (!text) return '';
            const div = document.createElement('div');
            div.innerText = text;
            return div.innerHTML;
        }

        function switchTab(tabId) {
            document.getElementById('tab-dashboard').style.display = 'none';
            document.getElementById('tab-courses').style.display = 'none';
            document.getElementById('tab-assignments').style.display = 'none';
            document.getElementById('tab-calendar').style.display = 'none';
            document.getElementById('tab-events').style.display = 'none';
            document.getElementById('course-details').style.display = 'none';

            document.getElementById('tab-' + tabId).style.display = 'block';
        }

        function toggleProfile() {
            const menu = document.getElementById('profile-menu');
            menu.style.display = menu.style.display === 'none' ? 'block' : 'none';
        }

        function openCourse(courseId) {
            const course = coursesData[courseId];
            if (!course) return;

            switchTab('courses');
            document.getElementById('tab-courses').style.display = 'none';
            document.getElementById('course-details').style.display = 'block';

            document.getElementById('course-title').innerText = course.title;
            document.getElementById('course-description').innerText = course.description || '';

            // 1. Рендерим публикации курса
            const pubContainer = document.getElementById('course-publications-list');
            if (course.publications.length === 0) {
                pubContainer.innerHTML = '<p>В этом курсе пока нет публикаций.</p>';
            } else {
                pubContainer.innerHTML = course.publications.map(p =>
                    `<article style="border-bottom: 1px solid #eee; margin-bottom: 10px; padding-bottom: 8px;">
                        <h4>${escapeHtml(p.title)} <small>(Автор: ${escapeHtml(p.author)})</small></h4>
                        <p>${escapeHtml(p.content)}</p>
                    </article>`
                ).join('');
            }

            // 2. Рендерим материалы (лекции) курса
            const matContainer = document.getElementById('course-materials-list');
            if (course.lectures.length === 0) {
                matContainer.innerHTML = '<li>Учебные материалы и лекции пока не добавлены.</li>';
            } else {
                matContainer.innerHTML = course.lectures.map(l =>
                    `<li>
                        <strong>[${escapeHtml(l.module)}] ${escapeHtml(l.title)}</strong> (${escapeHtml(l.date)})
                        ${l.description ? '— ' + escapeHtml(l.description) : ''}
                    </li>`
                ).join('');
            }

            // 3. Рендерим задания курса
            const assignContainer = document.getElementById('course-assignments-list');
            if (course.assignments.length === 0) {
                assignContainer.innerHTML = '<li>В этом курсе пока нет заданий.</li>';
            } else {
                assignContainer.innerHTML = course.assignments.map(a =>
                    `<li>
                        <strong>[${escapeHtml(a.module)}]</strong> ${escapeHtml(a.desc)}
                        (Срок: ${escapeHtml(a.deadline)}) —
                        <em>${a.status === 'submitted' ? 'Сдано' : 'Не сдано'}</em>
                        <button onclick="openAssignmentModalById(${a.id})">Открыть</button>
                    </li>`
                ).join('');
            }

            switchCourseTab('course-publications');
        }

        function switchCourseTab(subTabId) {
            document.getElementById('course-publications').style.display = 'none';
            document.getElementById('course-materials').style.display = 'none';
            document.getElementById('course-assignments').style.display = 'none';

            document.getElementById(subTabId).style.display = 'block';
        }

        function openAssignmentModalById(assignmentId) {
            const item = allAssignmentsList.find(a => a.id === assignmentId);
            if (!item) return;

            const modal = document.getElementById('assignment-modal');
            document.getElementById('modal-title').innerText = item.desc;
            document.getElementById('modal-course').innerText = item.course_title + ' (' + item.module + ')';
            document.getElementById('modal-deadline').innerText = item.deadline;
            document.getElementById('modal-max-score').innerText = item.max_score;

            let statusText = item.status === 'submitted' ? 'Сдано на проверку' : 'Не сдано';
            if (item.score !== null && item.score !== undefined) {
                statusText += ` (Оценка: ${item.score} / ${item.max_score})`;
            }
            document.getElementById('modal-status').innerText = statusText;

            const feedbackRow = document.getElementById('modal-feedback-row');
            if (item.feedback) {
                document.getElementById('modal-feedback').innerText = item.feedback;
                feedbackRow.style.display = 'block';
            } else {
                feedbackRow.style.display = 'none';
            }

            const uploadSection = document.getElementById('modal-upload-section');
            uploadSection.style.display = item.status === 'submitted' ? 'none' : 'block';

            modal.showModal();
        }

        function handleEventClick(courseId, targetTab, assignmentId) {
            openCourse(courseId);
            switchCourseTab(targetTab);

            if (assignmentId) {
                openAssignmentModalById(assignmentId);
            }
        }
    </script>

</body>
</html>