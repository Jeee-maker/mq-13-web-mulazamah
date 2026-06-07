<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Persaudaraan Putra Dan Putri MQ-13')</title>
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Main CSS -->
    <link rel="stylesheet" href="{{ asset('css/index.css') }}">
    @stack('css')
</head>
<body>

    @yield('content')

    @stack('scripts')
    <script>
        // Simple Accordion Script
        document.addEventListener('DOMContentLoaded', function() {
            const accordions = document.querySelectorAll('.accordion-header');
            accordions.forEach(acc => {
                acc.addEventListener('click', function() {
                    this.parentElement.classList.toggle('active');
                });
            });
        });
    </script>
</body>
</html>
