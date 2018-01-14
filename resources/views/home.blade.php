@extends('layouts.app') @section('content')
<div class="container-fulid" id="display-area">
    <div class="row no-gutters">
        <div class="col-sm-4 col-xl-3 col-12">
            <div id="search-bar" style="z-index:99;position: absolute;background-color: #f5f8fa">
                <form class="form-inline w-100" onsubmit="return search_submit(this)">
                    <div class="input-group w-100">
                        <div class="input-group-prepend">
                            <select class="custom-select" name="is_shop">
                                <option value="false" selected>地址</option>
                                <option value="true">店家</option>
                            </select>
                        </div>
                        <input type="search" placeholder="Search" aria-label="Search" class="form-control" name="name">
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-outline-success" dusk="search">Search</button>
                        </div>
                    </div>
                </form>
            </div>
            <div id="side-panel">
                <div class="list-group"></div>
            </div>
        </div>
        <div class="col-8 col-xl-9 d-none d-md-block">
            <div class="content">
                <div id="map"></div>
            </div>
        </div>
    </div>
</div>
<!-- Modal -->
<div class="modal fade" id="review_model" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">評分系統</h5>
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
@endsection @section('script')
<!-- Scripts -->
<script>
var default_radius = 100;
$('#search-bar').css('width', '100%')
$('#search-bar').css('padding', '5px')
var map;
var markers = [];
var searchbar_height = $('#search-bar').height() + $('#search-bar').css('padding').replace('px', '') * 2
$('#side-panel').css('margin-top', searchbar_height + 'px').css('height', 'calc(100vh - ' + navheight + 'px - ' + searchbar_height + 'px)')
$('.content').css('height', 'calc(100vh - ' + navheight + 'px)')
$('#map').css('height', '100%')

var generate_search_result = function(response) {
    $('#side-panel .list-group').html("");
    for (var item in response.data) {
        item = response.data[item]
        var id = item['_id']
        var name = item['name']
        console.log(name)
        var vicinity = item['vicinity']
        var lat = item['location']['coordinates'][1];
        var lng = item['location']['coordinates'][0];
        $('#side-panel .list-group').append(
            '<a href="#" class="list-group-item list-group-item-action flex-column align-items-start" onclick="select_address(\'' + id + '\',this)" data-lat="' + lat + '" data-lng="' + lng + '"><h5 class="mb-1">' + name + '</h5><small class="text-muted">' + vicinity + '</small></a>'
        );
    }
};

var generate_maker_and_list = function(response) {
    if (response.data.center)
        map.panTo({ lat: response.data.center[1], lng: response.data.center[0] });
    $('#side-panel .list-group').html("");
    clearMarkers();
    for (var item in response.data.data) {
        item = response.data.data[item]
        var name = item['name']
        console.log(name)
        var rating = item['rating']
        var vicinity = item['vicinity']
        var id = item['_id']
        var lat = item['location']['coordinates'][1];
        var lng = item['location']['coordinates'][0];
        $('#side-panel .list-group').append(
            '<a href="#" class="list-group-item list-group-item-action flex-column align-items-start" data-id="' + id + '" data-lat="' + lat + '" data-lng="' + lng + '" onclick="show_restaurant_detail(\'' + id + '\',this)"><div class="d-flex w-100 justify-content-between"><h5 class="mb-1">' + name + '</h5><small>' + rating + '/5</small></div><small class="text-muted">' + vicinity + '</small></a>'
        );
        addMarker({ lat: item['location']['coordinates'][1], lng: item['location']['coordinates'][0] })
    }
};

var generate_review = function(response) {
    console.log(response)
};

function search_submit(el) {
    event.preventDefault();
    var data = $(el).serialize()
    axios.post('{{route('search')}}', data)
        .then(function(response) {
            if (response.data.constructor === Object) {
                generate_maker_and_list(response)
            } else if (response.data.constructor === Array)
                generate_search_result(response)
        })
        .catch(function(error) {
            console.log(error);
        });
    return false;
}

function select_address(id, el, lat = false, lng = false) {
    var target = $(el)
    axios.post('{{route('search.near')}}', { id: id, radius: default_radius })
        .then(generate_maker_and_list)
        .catch(function(error) {
            console.log(error);
        });
    return false;
}

var show_restaurant_detail = (id, el) => {
    event.preventDefault();
    var el = $(el)
    axios.post('{{route('review')}}', { id: id })
        .then(generate_review)
        .catch(function(error) {
            console.log(error);
        });
    return false;
}

function initMap(lat = 24.178820, lng = 120.646705) {
    var gps = { lat: lat, lng: lng };

    map = new google.maps.Map(document.getElementById('map'), {
        zoom: 17,
        center: gps,
        mapTypeId: 'terrain'
    });

    addMarker(gps);
}

function addMarker(location) {
    var marker = new google.maps.Marker({
        position: location,
        map: map
    });
    markers.push(marker);
}

function setMapOnAll(map) {
    for (var i = 0; i < markers.length; i++) {
        markers[i].setMap(map);
    }
}

function clearMarkers() {
    setMapOnAll(null);
}

function showMarkers() {
    setMapOnAll(map);
}

function deleteMarkers() {
    clearMarkers();
    markers = [];
}

function activeGPS() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(geoYes, geoNo);
    } else {
        geoNo()
    }
}

function geoYes(e) {
    initMap(e.coords.latitude, e.coords.longitude)
    console.log(e.coords.latitude, e.coords.longitude)
    axios.post('{{route('search.gps')}}', { latitude: e.coords.latitude, longitude: e.coords.longitude, radius: default_radius })
        .then(generate_maker_and_list)
        .catch(function(error) {
            geoNo()
        });
}

function geoNo() {
    initMap();
}
(function($) {
    $(window).on("load", function() {
        $('#side-panel').mCustomScrollbar({
            theme:'minimal-dark'
        })
    });
})(jQuery);
</script>
<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCVzeYipfPzJuiHOUEu1CNUy9cYuaLBHaY&callback=activeGPS"></script>
@endsection