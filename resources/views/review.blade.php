@extends('layouts.app') @section('content')
<form method="post" autocomplete="on" onsubmit="return sendAddress(this)">
    <p>
        <label>星級:
            <label>
                <input name="rate" type="radio" value="1">
                一顆星
            </label>
            <label>
                <input name="rate" type="radio" value="2">
                兩顆星
            </label>
            <label>
                <input name="rate" type="radio" value="3">
                三顆星
            </label>
            <label>
                <input name="rate" type="radio" value="4">
                四顆星
            </label>
            <label>
                <input name="rate" type="radio" value="5">
                五顆星
            </label>
    </p>
    <input class="btn btn-success" type="submit" value="送出" />
    <input class="btn btn-danger" type="reset" value="重設" />
</form>
<script>
function sendAddress(el) {
    var data = $(el).serialize()
    event.preventDefault();
    axios.post('review', data)
        .then(function(response) {
            console.log(response);
        })
        .catch(function(error) {
            console.log(error);
        });
    return false;
}
</script>
@endsection