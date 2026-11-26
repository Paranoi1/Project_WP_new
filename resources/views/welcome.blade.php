
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="welcome-page">

    <div class="welcome-buttons">
        <a href="{{ route('login') }}"
           class="welcome-btn login-btn">
            Log in
        </a>

        <a href="{{ route('register') }}"
           class="welcome-btn register-btn">
            Register
        </a>
    </div>

</body>
</html>
