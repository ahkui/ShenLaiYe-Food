@extends('layouts.app') @section('content')
<form method="post" onsubmit="return searchName(this)">
     <div class="form-group">
    <label for="exampleStore">請輸入店名</label>
    <input type="text" class="form-control" name="exampleInput">
      <button type="submit" class="btn btn-primary">Submit</button>
  </div>
</form>
<script>
function searchName(el) {
    event.preventDefault();
    var data = $(el).serialize()
    axios.post('search', data)
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