<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <div>

        <h1>
            Manage Courses
        </h1>

        @if (session('success'))
            <div>
                {{ session('success') }}
            </div>
        @endif

        <h2>
            Add a New Course
        </h2>

        <form method="POST" action="{{ route('admin.classes.store') }}">
            @csrf

            <div>
                <label for="crs_title" class="block mb-2">
                    Course Name
                </label>

                <input
                    type="text"
                    name="crs_title"
                    id="crs_title"
                    value="{{ old('crs_title') }}"
                    required
                    class="w-full border rounded p-2"
                >
            </div>

            <div>
                <label>
                    Description
                </label>

                <textarea
                    name="crs_description"
                    id="crs_description"
                    rows="4"
                >{{ old('crs_description') }}</textarea>

            </div>

            <button type="submit">
                Create Course
            </button>
        </form>
            
        <a href="{{ route('dashboard') }}">
            Back to Dashboard
        </a>
        <hr>

        <h2>
            Existing Courses
        </h2>
            @forelse ($courses as $course)
                <div>

                    <h3>
                        {{ $course->crs_title }}
                    </h3>

                    <p>
                        {{ $course->crs_description }}
                    </p>

                    <a href="{{ route('admin.classes.modules.index', $course->crs_ID) }}">
                        Manage Modules
                    </a>

                </div>
            @empty
            <p>No courses have been created yet</p>
        @endforelse

    </div>
</body>

</html>
