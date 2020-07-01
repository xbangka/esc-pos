<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>@yield('title')</title>
    <!-- Favicon -->
    <link rel="shortcut icon" type="image/icon" href="{{asset('images/favicon.png')}}" />
    <!-- Font Awesome -->
    <link rel="stylesheet" type="text/css" href="assets/css/font-awesome.css">
    <!-- Bootstrap -->
    <link rel="stylesheet" type="text/css" href="assets/css/bootstrap.css">
    <!-- Slick slider -->
    <link rel="stylesheet" type="text/css" href="assets/css/slick.css" />
    <!-- Fancybox slider -->
    <link rel="stylesheet" type="text/css" href="assets/css/jquery.fancybox.css" media="screen" />
    <!-- Animate css -->
    <link rel="stylesheet" type="text/css" href="assets/css/animate.css" />
    <!-- Theme color -->
    <link rel="stylesheet" type="text/css" href="assets/css/theme-color/default-theme.css" id="switcher">

    <!-- Main Style -->
    <link rel="stylesheet" type="text/css" href="assets/style.css">
  
</head>

<body>

    <!-- BEGAIN PRELOADER -->
    <!-- <div id="preloader">
        <div id="status">&nbsp;</div>
    </div> -->
    <!-- END PRELOADER -->

    @section('navbar')
        @include('common.navbar')
    @show

    @yield('content')

    @section('footer')
        @include('common.footer')
    @show

    <!-- jQuery library -->
    <script type="text/javascript" src="assets/js/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <!-- Bootstrap -->
    <script type="text/javascript" src="assets/js/bootstrap.js"></script>
    <!-- Slick Slider -->
    <script type="text/javascript" src="assets/js/slick.js"></script>
    <!-- Add fancyBox -->
    <script type="text/javascript" src="assets/js/jquery.fancybox.pack.js"></script>
    <!-- Wow animation -->
    <script type="text/javascript" src="assets/js/wow.js"></script>
    <!-- Off-canvas Menu -->
    <script type="text/javascript" src="assets/js/classie.js"></script>

    <script type="text/javascript">

    </script>

</body>

</html>