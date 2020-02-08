<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title')</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.11.2/css/all.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bulma/0.8.0/css/bulma.min.css">
    <link rel="stylesheet" href="//cdn.jsdelivr.net/gh/highlightjs/cdn-release@9.18.0/build/styles/default.min.css">
    <link rel="stylesheet" href="/assets/codemirror-5.51.0/lib/codemirror.css">
    <link rel="stylesheet" href="/assets/codemirror-5.51.0/theme/dracula.css">
    @yield('styles')
    <link rel="stylesheet" href="/css/app.css">
</head>
<body>

    @include('partials._header')

    <section class="columns">
        <div class="column is-10 is-offset-1">
            @yield('content')
        </div>
    </section>

    @yield('scripts')
    <script src="//cdn.jsdelivr.net/gh/highlightjs/cdn-release@9.18.0/build/highlight.min.js"></script>
    <script>hljs.initHighlightingOnLoad()</script>
    <script>
        function copyToClipboard(id) {
            const target = event.target
            target.classList.remove('has-text-info')
            target.classList.add('has-text-warning')
            setTimeout(function() {
                target.classList.remove('has-text-warning')
                target.classList.add('has-text-info')
            }, 200)
            const data = document.getElementById(id).textContent
            const textarea = document.createElement('textarea')
            document.body.appendChild(textarea)
            textarea.value = data
            textarea.select()
            document.execCommand('copy')
            document.body.removeChild(textarea)
        }
    </script>
</body>
</html>
