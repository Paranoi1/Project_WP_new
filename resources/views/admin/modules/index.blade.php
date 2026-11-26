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
            Manage Modules
        </h1>

        <h2>
            Course: {{ $course->crs_title }}
        </h2>

        @if (session('success'))
            <div>
                {{ session('success') }}
            </div>
        @endif

        <h3>
            Add New Module
        </h3>

        <form
            method="POST"
            action="{{ route('admin.classes.modules.store', $course->crs_ID) }}"
        >
            @csrf

            <div>
                <label for="mod_title" class="block mb-2">
                    Module Title
                </label>

                <input
                    type="text"
                    name="mod_title"
                    id="mod_title"
                    value="{{ old('mod_title') }}"
                    maxlength="30"
                    required
                >

                @error('mod_title')
                    <p>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <button type="submit">
                Create Module
            </button>
        </form>

        <hr>


        <h3>
            Existing Modules
        </h3>

        @forelse ($course->modules as $module)
            <div>
                <h4>
                    {{ $module->mod_title }}
                </h4>

                <p>
                    Module ID: {{ $module->mod_ID }}
                </p>
            </div>
        @empty
            <p>No modules have been created for this course yet.</p>
        @endforelse

        <a href="{{ route('admin.classes.index') }}">
            Back to Courses
        </a>

    </div>
<body>
</html>
