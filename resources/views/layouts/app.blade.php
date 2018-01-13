<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <style>
    .custom-model {
        position: absolute;
        /*top: -200%;*/
        /*left: -200%;*/
        top: 0;
        left: 0;
        width: 100%;
        /*height: 100%;*/
        background-color: #f5f8fa;
        z-index: 999;
        /*opacity: 0;*/
        /*display: none;*/
    }
    </style>
</head>

<body style="height:100vh">
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-dark bg-dark fixed-top">
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navBar">
                <span class="navbar-toggler-icon"></span>
            </button>
            <a class="navbar-brand" href="{{route('home')}}">{{config('app.name', 'Laravel')}}</a>
            <div class="collapse navbar-collapse" id="navBar">
                <ul class="navbar-nav mr-auto mt-2 mt-md-0">
                    @auth
                    <li class="nav-item active">
                        <a class="nav-link" href="#">Home <span class="sr-only">(current)</span></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#" data-toggle="modal" data-target="#exampleModal">評分</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Link</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link disabled" href="#">Disabled</a>
                    </li>
                    @endauth
                </ul>
                <ul class="navbar-nav mt-2 mt-md-0">
                    @guest
                    <li class="nav-item">
                        <a class="nav-link disabled" href="{{route('login')}}">登入</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link disabled" href="{{route('register')}}">註冊</a>
                    </li>
                    @endguest @auth
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('logout') }}" onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">登出</a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            {{ csrf_field() }}
                        </form>
                    </li>
                    @endauth
                </ul>
            </div>
        </nav>
        @yield('content')
    </div>
    <script src="{{ asset('js/app.js') }}"></script>
    <script>
    var navheight = Math.ceil($('nav').height() + $('nav').css('padding-top').replace('px', '') * 2)
    $('body').css('padding-top', navheight + 'px')
    </script>
    @yield('script')
</body>

</html>