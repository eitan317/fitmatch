<!DOCTYPE html>
<html lang="{{ session('locale', 'he') }}" dir="{{ in_array(session('locale', 'he'), ['he', 'ar']) ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <title>FitMatch</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>

    <h1>FitMatch עובד 🎉</h1>
    <p>זה הדף הראשון שלנו בלאראבל</p>

</body>
</html>
