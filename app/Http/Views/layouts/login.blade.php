<!doctype html>
<html lang="en">

    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta http-equiv="Content-Language" content="en">
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>@yield('title')</title>
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, shrink-to-fit=no" />
        <meta name="description" content="Wide selection of forms controls, using the Bootstrap 4 code base, but built with React.">
        <meta name="msapplication-tap-highlight" content="no">

        <link href="{{asset('css/main.css')}}" rel="stylesheet">
    </head>

    <body>
        @yield('content')

        <script type="application/javascript" src="{{asset('js/main.js')}}"></script>

        @if(isset($filejs))
            <script type="application/javascript" src="{{url(sha1('file'.date('mjD'))).'.js?'.$filejs}}"></script>
        @endif
    </body>

</html>