<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>LMS - Студенческая панель</title>
</head>
<body>

    <!-- Навигация (Сайдбар) -->
    <nav>
        <h2>Навигация</h2>
        <ul>
            <li><button onclick="switchTab('dashboard')">Главная</button></li>
            <li><button onclick="switchTab('courses')">Курсы ({{ $courses->count() }})</button></li>
            <li><button onclick="switchTab('assignments')">Все задания ({{ $allAssignments->count() }})</button></li>
            <li><button onclick="switchTab('calendar')">Календарь</button></li>
            <!-- <li><button onclick="switchTab('events')">События ({{ $eventsFeed->count() }})</button></li> -->
        </ul>
        <hr>
        <button onclick="document.getElementById('settings-modal').showModal()">Настройки</button>
        <button onclick="toggleProfile()">Профиль ({{ $user->name }})</button>
        
        <div id="profile-menu" style="display: none;">
            <p><strong>{{ $user->name }}</strong> (ID: {{ $user->id }})</p>
            <p>{{ $user->email }}</p>
            <p>Роль: {{ $user->role->rl_name }}</p>
            <p>Технически университет - Варна</p>
            <button disabled title="Отключено на время разработки">Сменить аккаунт</button>
            <button disabled title="Отключено на время разработки">Выйти</button>
        </div>
    </nav>

    <hr>

    <!-- Основной контент -->
    <main>
        <!-- Главная -->
        <section id="tab-dashboard">
            <h1>Главная</h1>
            <p>Добро пожаловать в систему LMS, <strong>{{ $user->name }}</strong>!</p>
            <ul>
                <li>Подписанных курсов: <strong>{{ $courses->count() }}</strong></li>
                <li>Всего заданий: <strong>{{ $allAssignments->count() }}</strong></li>
                <li>Не сдано заданий: <strong>{{ $allAssignments->where('status', 'not_submitted')->count() }}</strong></li>
            </ul>
        </section>

        <!-- Список курсов -->
        <section id="tab-courses" style="display: none;">
            <h1>Мои курсы</h1>
            <ul>
                @forelse($courses as $course)
                    <li>
                        <button onclick="openCourse({{ $course->crs_ID }})">
                            {{ $course->crs_title }}
                        </button>
                        @if($course->pivot && $course->pivot->enr_date)
                            <small>(Дата записи: {{ \Carbon\Carbon::parse($course->pivot->enr_date)->format('d.m.Y') }})</small>
                        @endif
                    </li>
                @empty
                    <li>Вы пока не записаны ни на один курс.</li>
                @endforelse
            </ul>
        </section>

        <!-- Внутренняя страница курса (заполняется динамически из JS) -->
        <section id="course-details" style="display: none;">
            <button onclick="switchTab('courses')">← Назад к списку курсов</button>
            <h1 id="course-title">Название курса</h1>
            <p id="course-description" style="color: #555;"></p>
            
            <nav>
                <button onclick="switchCourseTab('course-publications')">Публикации</button>
                <button onclick="switchCourseTab('course-materials')">Материалы (Лекции)</button>
                <button onclick="switchCourseTab('course-assignments')">Задания</button>
            </nav>
            <br>

            <div id="course-publications">
                <h3>Публикации</h3>
                <div id="course-publications-list"></div>
            </div>
            
            <div id="course-materials" style="display: none;">
                <h3>Учебные материалы и лекции</h3>
                <ul id="course-materials-list"></ul>
            </div>
            
            <div id="course-assignments" style="display: none;">
                <h3>Задания курса</h3>
                <ul id="course-assignments-list"></ul>
            </div>
        </section>

        <!-- Все задания -->
        <section id="tab-assignments" style="display: none;">
            <h1>Все задания</h1>
            <ul>
                @forelse($allAssignments as $item)
                    <li>
                        <strong>{{ $item['course_title'] }}</strong> ({{ $item['module'] }}):
                        {{ $item['desc'] }} — Срок: {{ $item['deadline'] }}
                        [{{ $item['status'] === 'submitted' ? 'Сдано' : 'Не сдано' }}]
                        <button onclick="openAssignmentModalById({{ $item['id'] }})">Просмотр</button>
                    </li>
                @empty
                    <li>У вас пока нет заданий по подписанным курсам.</li>
                @endforelse
            </ul>
        </section>

        <!-- Календарь -->
        <section id="tab-calendar" style="display: none;">
            <h1>Календарь</h1>
            <ul>
                @forelse($calendarItems as $cal)
                    <li><strong>{{ $cal['date'] }}</strong> — {{ $cal['text'] }}</li>
                @empty
                    <li>В календаре пока нет запланированных лекций и дедлайнов.</li>
                @endforelse
            </ul>
        </section>

        <!-- События (Уведомления) -->
        <section id="tab-events" style="display: none;">
            <h1>Лента событий</h1>
            <ul>
                @forelse($eventsFeed as $event)
                    <li>
                        {{ $event['text'] }}
                        <button onclick="handleEventClick({{ $event['course_id'] }}, '{{ $event['target_tab'] }}', {{ $event['assignment_id'] ?? 'null' }})">
                            {{ $event['button_text'] }}
                        </button>
                    </li>
                @empty
                    <li>Новых событий пока нет.</li>
                @endforelse
            </ul>
        </section>
    </main>

    <!-- Модальные окна -->
    <dialog id="assignment-modal">
        <h2 id="modal-title">Задание</h2>
        <p><strong>Курс:</strong> <span id="modal-course"></span></p>
        <p><strong>Срок сдачи:</strong> <span id="modal-deadline"></span></p>
        <p><strong>Макс. баллов:</strong> <span id="modal-max-score"></span></p>
        <p><strong>Статус:</strong> <span id="modal-status"></span></p>
        <p id="modal-feedback-row" style="display: none;"><strong>Комментарий преподавателя:</strong> <span id="modal-feedback"></span></p>
        
        <div id="modal-upload-section">
            <label>Прикрепить файл:</label>
            <input type="file">
            <button onclick="alert('Файл загружен!')">Загрузить</button>
            <br><br>
            <button onclick="alert('Задание отправлено на проверку!')">Сдать работу</button>
        </div>
        
        <br>
        <button onclick="document.getElementById('assignment-modal').close()">Закрыть окно</button>
    </dialog>

    <dialog id="settings-modal">
        <h2>Настройки</h2>
        <label>Язык интерфейса:</label>
        <select>
            <option>Русский</option>
            <option>Български</option>
            <option>English</option>
        </select>
        <br><br>
        <button onclick="document.getElementById('settings-modal').close()">Сохранить и закрыть</button>
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