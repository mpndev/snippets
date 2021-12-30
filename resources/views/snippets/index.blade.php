<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="canonical" href="{{url('/')}}"/>
    <title></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.11.2/css/all.css">
</head>
<body class="has-background-grey-lighter" style="min-height: 100vh;">
    <div id="app">

        <message></message>

        <navbar></navbar>

        <div class="section has-margin-top-30">
            <div>
                <router-view :key="$route.fullPath"></router-view>
            </div>
        </div>

        <div class="section">
            <faq />
        </div>
    </div>

    <script src="/js/app.js"></script>
</body>
</html>
