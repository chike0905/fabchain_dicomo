@extends('layouts.app')

@section('content')
<script src="{{ asset('js/sha256.js') }}"></script>
<div class="content row text-center">
    <div class="well col-sm-8 col-sm-offset-2">
        <form class="form-horizontal" id="printinfo">
            <div class="form-group">
                <label for="maker" class="control-label col-sm-3">Ethereum Addres of Maker</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" id="maker" name="maker">
                </div>
            </div>
            <div class="form-group">
                <label for="designer" class="control-label col-sm-3">Ethereum Addres of designer</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" id="designer" name="designer">
                </div>
            </div>
            <div class="form-group">
                <label for="objname" class="control-label col-sm-3">Object Name</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" id="objname" name="objname">
                </div>
            </div>
            <div class="form-group">
                <label for="g-code" class="control-label col-sm-3">G-code File</label>
                <div class="col-sm-9">
                    <input type="file" id="g-code" name="g-code" style="display:none;" accept=".gcode" onchange="$('#fake_input_file').val($(this).val());">
                    <input type="button"  class="btn btn-primary col-sm-4" value="ファイル選択" onClick="$('#g-code').click();">
                    <input id="fake_input_file" class="col-sm-8" readonly type="text" placeholder="ファイル未選択"  onClick="$('#g-code').click();">
                </div>
            </div>
            <div class="form-group">
                <label for="g-codehash" class="control-label col-sm-3">G-code Hash</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" id="g-codehash" name="g-codehash" readonly>
                </div>
            </div>
            <div class="form-group">
                <label for="apikey" class="control-label col-sm-3">ApiKey for Octprint</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" id="apikey" name="apikey">
                </div>
            </div>
            <input type="button" value="Print" class="btn btn-primary col-sm-4 col-sm-offset-4 " onclick="print()">
        </form>
    </div>
</div>
<script>
var obj1 = document.getElementById("g-code");

//ダイアログでファイルが選択された時
obj1.addEventListener("change",function(evt){

    var file = evt.target.files;
    var reader = new FileReader();
    reader.readAsText(file[0]);
    reader.onload = function(ev){
        text = reader.result;
        var shaObj = new jsSHA("SHA-256", "TEXT", 1);
        shaObj.update(text);
        var sha256digest = shaObj.getHash("HEX");

        $("#g-codehash").val(sha256digest);
    }
},false);
function print(){
    var fd = new FormData($('#printinfo').get(0));

}
</script>
@endsection
