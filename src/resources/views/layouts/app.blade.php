<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Laravel') }}</title>
    
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Lobster&family=Lobster+Two:ital,wght@0,400;0,700;1,400;1,700&display=swap" rel="stylesheet">
    
    @yield('page_styles')
</head>
<body>
    <div id="app">
        <header class="global-header">
            <div class="header-content">
                <a href="/" class="app-logo">mogitate</a>
                </div>
            <div class="header-border"></div>
        </header>

        @yield('content')
        
    </div>
</body>
</html>