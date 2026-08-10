<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="app-name" content="{{ config('settings.site_title') }}">

    <title inertia>
        {{ config('settings.site_title') }}
    </title>

    {{-- Icon --}}
    <link rel="icon" href="{{ asset('img/logo/favicon.png') }}">
    <link rel="shortcut icon" href="{{ asset('img/logo/favicon.png') }}">

    {{-- Google Font --}}
    <link href="https://fonts.gstatic.com" rel="preconnect">
    <link href="https://fonts.googleapis.com/css2?family=Rubik:wght@400;500;600;700&display=swap" rel="stylesheet">

    {{-- Core  --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" />
    <script defer src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/js/bootstrap.bundle.min.js"></script>

    <!-- MathType -->
    <script defer src="https://www.wiris.net/demo/plugins/app/WIRISplugins.js?viewer=image"></script>

    {{-- Scripts --}}
    @routes
    @vite(['resources/js/app.js', "resources/js/Pages/{$page['component']}.vue"])
    @inertiaHead
</head>

<body>
    @inertia
</body>

</html>
