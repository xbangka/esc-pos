<!doctype html>
<html lang="en">

    <head>
        <meta charset="utf-8">

        <meta http-equiv="X-UA-Compatible" content="IE=edge">

        <meta http-equiv="Content-Language" content="en">

        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, shrink-to-fit=no" />

        <meta name="description" content="Wide selection of forms controls, using the Bootstrap 4 code base, but built with React.">

        <meta name="msapplication-tap-highlight" content="no">

        <title>@yield('title')</title>

        <link rel="stylesheet" href="{{asset('css/main.css?sds')}}">

        <link rel="stylesheet" href="{{asset('css/responsive.dataTables.min.css')}}">

        <link rel="stylesheet" href="{{asset('css/dataTables.bootstrap4.min.css')}}">
    </head>

    <body>

        <div class="app-container app-theme-white body-tabs-shadow fixed-header">
            
            @section('topbar')
                @include('common.topbar')
            @show

            <div class="app-main">

                @section('sidebar')
                    @include('common.sidebar')
                @show
                
                <div class="app-main__outer">
                    <div class="app-main__inner">

                        @yield('content')
                        
                    </div>

                    @section('footer')
                        @include('common.footer')
                    @show

                </div>
            </div>
        </div>

        <script type="application/javascript" src="{{asset('js/main.js')}}"></script>

        <script type="application/javascript" src="{{asset('js/jquery-3.3.1.min.js')}}"></script>

        <script type="application/javascript" src="{{asset('js/sweetalert2.all.min.js')}}"></script>
        
        <script type="application/javascript" src="{{asset('js/jquery.dataTables.min.js')}}"></script>

        <script type="application/javascript" src="{{asset('js/dataTables.responsive.min.js')}}"></script>

        <script type="application/javascript" src="{{asset('js/dataTables.bootstrap4.min.js')}}"></script>

        @php
            $version_dev = config('app.env')=='local' ? '_dev' : '' ;
        @endphp
        
        <script type="application/javascript" src="{{asset('js/vue'.$version_dev.'/vue.js')}}"></script>
        
        <script type="application/javascript" src="{{asset('js/axios.min.js')}}"></script>

        <script type="application/javascript" src="{{asset('js/crypto-js.js')}}"></script>

        <script type="application/javascript" src="{{asset('js/Encryption.js')}}"></script>

        @if(isset($filejs))
            <script type="application/javascript" src="{{url(sha1('file'.date('mjD'))).'.js?'.$filejs}}"></script>
        @endif

        <script type="application/javascript">
            function logoutAction(){
                Swal.fire({
                    title: 'Logout',
                    text: 'yakin Anda ingin keluar ?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: ' IYA ',
                    cancelButtonText: 'TIDAK'
                }).then((result) => {
                    if(result.value){
                        window.location = "{{url('logout')}}";
                    }
                });
            }
        </script>
    </body>
</html>