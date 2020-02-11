<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
        @auth
        function toggleFavorite(snippet_id) {
            let favorite_snippet = document.getElementsByClassName('favorite-snipet-' + snippet_id)[0]
            let current_class = favorite_snippet.firstElementChild.classList.contains('has-text-warning') ? 'has-text-warning' : 'has-text-dark'
            const method = current_class === 'has-text-warning' ? 'DELETE' : 'POST';
            fetch('http://snippets.test/{{ auth()->user()->name }}/favorite-snippets/' + snippet_id, {
                method: method,
                credentials: 'same-origin',
                headers: {
                    'Content-Type': 'application/json',
                    "X-CSRF-Token": document.head.querySelector("[name~=csrf-token][content]").content
                }
            }).then(response => {
                favorite_snippet.firstElementChild.classList.toggle('has-text-warning')
                favorite_snippet.firstElementChild.classList.toggle('has-text-dark')
                let target = document.querySelector(['[href="{{ route('favorite-snippets', ['user' => auth()->user()->name]) }}"'])
                if (method === 'POST') {
                    target.innerHTML = 'My collection (' + ((target.innerHTML.trim().replace('(', '').replace(')', '').split(' ')[2] | 0) + 1) + ')'
                } else {
                    target.innerHTML = 'My collection (' + ((target.innerHTML.trim().replace('(', '').replace(')', '').split(' ')[2] | 0) - 1) + ')'
                }
            })
        }
        @else
        function toggleFavorite() {
            window.location = '/login'
        }
        @endauth

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
