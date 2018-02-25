@extends('layouts.app') @section('navbar-left')
<li class="nav-item">
    <a class="nav-link" href="#" onclick="show_suggest_restaurant_detail();return false">今日推薦</a>
</li>
@endsection @section('content')
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
        <div class="col-8 col-xl-9 d-none d-sm-block">
            <div class="content">
                <div id="map"></div>
            </div>
        </div>
    </div>
</div>
<!-- Modal -->
@endsection @section('script')
<!-- Scripts -->
<script>
var prev_link;
var prev_data;
var is_loading = false
var loading_stop = () => {
    $('#axios-progress').fadeOut(200)
    is_loading = false
};
var loading_start = () => {
    $('#axios-progress').fadeIn(200)
    is_loading = true
};
var default_radius = 100;
$('#search-bar').css('width', '100%')
$('#search-bar').css('padding', '5px')
var map;
var markers = [];
var searchbar_height = $('#search-bar').height() + $('#search-bar').css('padding').replace('px', '') * 2
$('#side-panel').css('margin-top', searchbar_height + 'px').css('height', 'calc(100vh - ' + navheight + 'px - ' + searchbar_height + 'px)')
$('.content').css('height', 'calc(100vh - ' + navheight + 'px)')
$('#map').css('height', '100%')
var loading_error = (error) => {
    loading_stop();
    console.error(error)
}

var generate_search_result = function(response) {
    loading_stop();
    $('#side-panel .list-group').html("");
    for (var item in response.data) {
        item = response.data[item]
        var id = item['_id']
        var name = item['name']
        var vicinity = item['vicinity']
        var lat = item['location']['coordinates'][1];
        var lng = item['location']['coordinates'][0];
        $('#side-panel .list-group').append(
            '<a href="#" class="list-group-item list-group-item-action flex-column align-items-start" onclick="select_address(\'' + id + '\',this)" data-lat="' + lat + '" data-lng="' + lng + '"><h5 class="mb-1">' + name + '</h5><small class="text-muted">' + vicinity + '</small></a>'
        );
    }
};

var generate_maker_and_list = function(response) {
    loading_stop();
    if (response.data.center)
        map.panTo({ lat: response.data.center[1], lng: response.data.center[0] });
    $('#side-panel .list-group').html("");
    deleteMarkers();
    for (var item in response.data.data) {
        item = response.data.data[item]
        var name = item['name']
        var rating = item['rating']
        var vicinity = item['vicinity']
        var id = item['_id']
        var lat = item['location']['coordinates'][1];
        var lng = item['location']['coordinates'][0];
        $('#side-panel .list-group').append(
            '<a href="#" class="list-group-item list-group-item-action flex-column align-items-start" data-id="' + id + '" data-lat="' + lat + '" data-lng="' + lng + '" onclick="show_restaurant_detail(\'' + id + '\')"><div class="d-flex w-100 justify-content-between"><h5 class="mb-1">' + name + '</h5><small>' + rating + '/5</small></div><small class="text-muted">' + vicinity + '</small></a>'
        );
        addMarker({ lat: item['location']['coordinates'][1], lng: item['location']['coordinates'][0] })
    }
};

function search_submit(el) {
    event.preventDefault();
    if (!is_loading) {
        loading_start();
        var data = $(el).serialize()
        axios.post('{{route("search")}}', data)
            .then(function(response) {
                if (response.data.constructor === Object) {
                    generate_maker_and_list(response)
                } else if (response.data.constructor === Array)
                    generate_search_result(response)
            })
            .catch(loading_error);
    }
    return false;
}

function select_address(id, el, lat = false, lng = false) {
    event.preventDefault();
    if (!is_loading) {
        loading_start();
        var target = $(el)
        axios.post('{{route("search.near")}}', { id: id, radius: default_radius })
            .then(generate_maker_and_list)
            .catch(loading_error);
    }
    return false;
}



var generate_review = function(response) {
    loading_stop();
    review_modal_load((e) => {
        var el = $(e.currentTarget)
        var reviews_count = el.find("#reviews_count>a:first")
        var reviews_rating = el.find('#reviews_rating')
        var title = el.find('.modal-title')
        var id = el.find('[name=id]')
        title.html(response.data.name)
        reviews_count.html(response.data.reviews_count)
        reviews_rating.html(response.data.rating)
        el.find('#rating').barrating('set', parseInt(response.data.rating))
        el.find('#user-rating').barrating('set', response.data.user_rate)
        id.val(response.data._id)
        var comment_area = el.find('#comment_area')
        var name
        var comment
        var date
        var comment_data = response.data.restaurant_comments
        comment_area.html("")
        for (key in comment_data) {
            if (comment_data.hasOwnProperty(key) && // These are explained
                /^0$|^[1-9]\d*$/.test(key) && // and then hidden
                key <= 4294967294 // away below
            ) {
                name = comment_data[key].user.email
                comment = comment_data[key].comment
                date = comment_data[key].updated_at
                comment_area.prepend(
                    '<div class="list-group-item flex-column align-items-start"><div class="d-flex w-100 justify-content-between"><h5 class="mb-1">' + name + '</h5><small>' + date + '</small></div><small class="text-muted">' + comment + '</small></div>'
                );
            }
        }
    }, (e) => {}, (form, modal) => {
        var handle_success = (response) => {
            modal.modal('hide')
        }
        axios.put('{{route("review.save")}}', $(form).serialize())
            .then(handle_success)
            .catch(loading_error);
    })
    $('#review-modal').modal()
};

var show_restaurant_detail = (id, addon_title = null) => {
    event.preventDefault();
    if (!is_loading) {
        loading_start();
        axios.post('{{route("review")}}', { id: id })
            .then((response) => {
                if (addon_title)
                    response.data.name = addon_title + response.data.name;
                generate_review(response)
            })
            .catch(loading_error);
    }
    return false;
}



var suggest_id = false;
var show_suggest_restaurant_detail = () => {
    if (!suggest_id) {
        if (!is_loading) {
            loading_start();
            axios.post('{{route("suggest")}}')
                .then((response) => {
                    suggest_id = response.data
                    loading_stop()
                    show_suggest_restaurant_detail()
                })
                .catch(loading_error)
        }
    } else 
        show_restaurant_detail(suggest_id, '今日推薦 - ')
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
    loading_start()
    if (navigator.geolocation)
        navigator.geolocation.getCurrentPosition(geoYes, geoNo);
    else
        geoNo()
}

function geoYes(e) {
    initMap(e.coords.latitude, e.coords.longitude)
    axios.post('{{route("search.gps")}}', { latitude: e.coords.latitude, longitude: e.coords.longitude, radius: default_radius })
        .then(generate_maker_and_list)
        .catch(function(error) {
            geoNo()
        });
}

function geoNo() {
    initMap();
    loading_stop()
    alert("GPS 獲取失敗！請手動輸入店家或地址。")
}

(function($) {
    $(window).on("load", function() {
        $('#side-panel').mCustomScrollbar({
            theme: 'minimal-dark'
        })
    });
})(jQuery);
</script>
<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCVzeYipfPzJuiHOUEu1CNUy9cYuaLBHaY&callback=activeGPS"></script>
@endsection