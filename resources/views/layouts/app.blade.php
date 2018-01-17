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
    <link href="https://cdnjs.cloudflare.com/ajax/libs/malihu-custom-scrollbar-plugin/3.1.5/jquery.mCustomScrollbar.css" rel="stylesheet">
</head>

<body>
    <div id="app">
        <nav class="navbar navbar-expand-sm navbar-dark bg-dark fixed-top">
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navBar">
                <span class="navbar-toggler-icon"></span>
            </button>
            <a class="navbar-brand" href="{{route('home')}}">{{config('app.name', 'Laravel')}}</a>
            <div class="collapse navbar-collapse" id="navBar">
                <ul class="navbar-nav mr-auto mt-2 mt-sm-0">
                    @auth
                    @yield('navbar-left')
                    @endauth
                </ul>
                <ul class="navbar-nav mt-2 mt-sm-0">
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
        <div id="axios-progress" class="progress">
            <div class="w-100 progress-bar progress-bar-striped progress-bar-animated"></div>
        </div>
        @yield('content')
    </div>
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/malihu-custom-scrollbar-plugin/3.1.5/jquery.mCustomScrollbar.min.js"></script>
    <script>
    var navheight = Math.ceil($('nav').height() + $('nav').css('padding-top').replace('px', '') * 2)
    $('body').css('padding-top', navheight + 'px')
    </script>
    @yield('script')
    <script type="text/javascript">
    </script>
    @component('components.modal.review') @endcomponent
</body>

</html>