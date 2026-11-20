
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gray-100 flex items-center justify-center">

    <div class="flex gap-6">
        <a href="{{ route('login') }}"
           class="px-8 py-3 bg-red-600 text-white
                  hover:bg-red-700 transition">
            Log in
        </a>

        <a href="{{ route('register') }}"
           class="px-8 py-3 bg-gray-600 text-white
                  hover:bg-gray-700 transition">
            Register
        </a>
    </div>

</body>
</html>
