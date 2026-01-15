<!DOCTYPE html>
<html lang="{{ session('locale', 'he') }}" dir="{{ in_array(session('locale', 'he'), ['he', 'ar']) ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>FitMatch - התחברות</title>
    @include('partials.adsense')
    @include('partials.schema-ld')
</head>
<body>

<h1>התחברות</h1>
<p>אנא הזן אימייל כדי להיכנס</p>

<form id="login-form">
    <label>אימייל:</label><br>
    <input 
        type="email" 
        id="email" 
        name="email"
        placeholder="name@example.com"
        required
    >
    <br><br>

    <button type="submit">כניסה</button>
</form>

<p>
    <a href="/">חזרה לדף הבית</a>
</p>

<script src="/site/script.js"></script>

</body>
</html>
