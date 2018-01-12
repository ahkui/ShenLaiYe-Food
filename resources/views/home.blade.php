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
</head>

<body style="height:100vh">
    <div id="app">
        <nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top">
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navBar">
                <span class="navbar-toggler-icon"></span>
            </button>
            <a class="navbar-brand" href="#">{{config('app.name', 'Laravel')}}</a>
            <div class="collapse navbar-collapse" id="navBar">
                <ul class="navbar-nav mr-auto mt-2 mt-lg-0">
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
                </ul>
            </div>
        </nav>
        <div class="container-fulid" id="display-area">
            <div class="row no-gutters">
                <div class="col-4">
                    <div id="search-bar" style="z-index:999;position: absolute;background-color: #f5f8fa">
                        <form class="form-inline w-100" onsubmit="return search_submit(this)">
                            <input type="search" placeholder="Search" aria-label="Search" class="w-75 form-control" name="name">
                            <button type="submit" class="w-25 btn btn-outline-success">Search</button>
                        </form>
                    </div>
                    <div id="side-panel">
                        <div class="list-group">
                            <div class="list-group-item flex-column align-items-start">
                                <div class="d-flex w-100 justify-content-between">
                                    <h5 class="mb-1">List group item heading</h5>
                                    <small>3 days ago</small>
                                </div>
                                <p class="mb-1">Donec id elit non mi porta gravida at eget metus. Maecenas sed diam eget risus varius blandit.</p>
                                <a class="btn btn-primary nav-link" href="#" data-toggle="modal" data-target="#exampleModal">評分</a>
                            </div>
                            <a href="#" class="list-group-item list-group-item-action flex-column align-items-start">
                                <div class="d-flex w-100 justify-content-between">
                                    <h5 class="mb-1">List group item heading</h5>
                                    <small class="text-muted">3 days ago</small>
                                </div>
                                <p class="mb-1">Donec id elit non mi porta gravida at eget metus. Maecenas sed diam eget risus varius blandit.</p>
                                <small class="text-muted">Donec id elit non mi porta.</small>
                            </a>
                            <a href="#" class="list-group-item list-group-item-action flex-column align-items-start">
                                <div class="d-flex w-100 justify-content-between">
                                    <h5 class="mb-1">List group item heading</h5>
                                    <small class="text-muted">3 days ago</small>
                                </div>
                                <p class="mb-1">Donec id elit non mi porta gravida at eget metus. Maecenas sed diam eget risus varius blandit.</p>
                                <small class="text-muted">Donec id elit non mi porta.</small>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-8">
                    <div id="map"></div>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">評分系統</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="form-group">
                            <p>
                                <label>星級:
                                    <label>
                                        <input name="rate" type="radio" value="1"> 一顆星
                                    </label>
                                    <label>
                                        <input name="rate" type="radio" value="2"> 兩顆星
                                    </label>
                                    <label>
                                        <input name="rate" type="radio" value="3"> 三顆星
                                    </label>
                                    <label>
                                        <input name="rate" type="radio" value="4"> 四顆星
                                    </label>
                                    <label>
                                        <input name="rate" type="radio" value="5"> 五顆星
                                    </label>
                            </p>
                        </div>
                        <div class="form-group">
                            <label for="exampleInputPassword1">評論</label>
                            <input type="text" class="form-control" id="exampleInputPassword1" placeholder="請輸入評論">
                        </div>
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="exampleCheck1">
                            <label class="form-check-label" for="exampleCheck1">確認無誤</label>
                        </div>
                        <button type="submit" class="btn btn-primary">送出</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}"></script>
    <script>
    var navheight = Math.ceil($('nav').height() + $('nav').css('padding-top').replace('px', '') * 2)
    $('#search-bar').css('width', 'calc(100% - 16px)')
    $('#search-bar').css('padding', '5px')
    var searchbar_height = $('#search-bar').height() + $('#search-bar').css('padding').replace('px', '') * 2
    $('body').css('padding-top', navheight + 'px')
    $('#side-panel').css('padding-top', searchbar_height + 'px').css('height', 'calc(100vh - ' + navheight + 'px)')
    $('#map').css('height', 'calc(100vh - ' + navheight + 'px)')
    $('#side-panel').css('overflow-y', 'auto')

    function search_submit(el) {
        event.preventDefault();
        var data = $(el).serialize()
        axios.post('search', data)
            .then(function(response) {
                $('#side-panel .list-group').html("");
                console.log(response.data)
                for (var item in response.data) {
                    item = response.data[item]
                    var id = item['_id']
                    var name = item['name']
                    $('#side-panel .list-group').append("<a class=\"btn btn-light\" onclick=\"select_address('" + id + "')\">" + name + "</a>");

                }
            })
            .catch(function(error) {
                console.log(error);
            });
        return false;
    }

    function select_address(id) {
        axios.post('search/near', { id: id })
            .then(function(response) {
                $('#side-panel .list-group').html("");
                for (var item in response.data) {
                    item = response.data[item]
                    $('#side-panel .list-group').append("<p>" + item['name'] + "</p>");
                }
            })
            .catch(function(error) {
                console.log(error);
            });
        return false;
    }


    function initMap() {
        var uluru = { lat: 24.178820, lng: 120.646705 };
        var map = new
        google.maps.Map(document.getElementById('map'), {
            zoom: 20,
            center: uluru
        });
        var marker = new google.maps.Marker({
            position: uluru,
            map: map
        });
    }
    </script>
    <script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCVzeYipfPzJuiHOUEu1CNUy9cYuaLBHaY&callback=initMap"></script>
</body>

</html>