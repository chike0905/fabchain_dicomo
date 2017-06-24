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
                <label for="gcodehash" class="control-label col-sm-3">G-code Hash</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" id="gcodehash" name="gcodehash" readonly>
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
    <div id="ethinfo" class="panel panel-default col-sm-10 col-sm-offset-1">
        <div class="panel-heading">
            <h3>Object Info Saved on Blockchain</h3>
        </div>
        <div class="panel-body">
            <table class="table table-striped">
                <tr id="result">
                    <th>Result</th><td></td>
                </tr>
                <tr id="TXadd">
                    <th>Object Print TX</th><td></td>
                </tr>
                <tr id="cntadd">
                    <th>Object Contract Address</th><td></td>
                </tr>
                <tr id="makeradd">
                    <th>Ethereum Addres of Maker</th><td></td>
                </tr>
                <tr id="designadd">
                    <th>Ethereum Addres of designer</th><td></td>
                </tr>
                <tr id="objname">
                    <th>Object Name</th><td></td>
                </tr>
                <tr id="ghash">
                    <th>G-code Hash</th><td></td>
                </tr>
            </table>
        </div>
    </div>
    <div class="panel  panel-default col-sm-10 col-sm-offset-1">
        <div class="panel-heading">
            <h3>Printing info</h3>
        </div>
        <div class="panel-body">
            now printing?
        </div>
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

        $("#gcodehash").val(sha256digest);
    }
},false);

var getcntflag = 0;
function print(){
    var fd = new FormData($('#printinfo').get(0));
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $.ajax({
        type : "POST",
            data: fd,
            url : './print',
            processData: false,
            contentType: false,
            dataType : "json",
            success : function(json) {
                console.log(json);
                if(json.geth.result){
                   $("#ethinfo #TXadd td").text(json.geth.result);
                   $("#ethinfo #result td").text("success");
                   var id = setInterval(function(){
                       var getcnt = getcontinfo(json.geth.result);
                       if(getcntflag == 1){　
                           clearInterval(id);
                       }}, 3000);
                }else{
                   $("#ethinfo #result td").text(json.geth.error.code+":"+json.geth.error.message);
                }
            },
            error : function(XMLHttpRequest, textStatus, errorThrown) {
                console.log( textStatus + ":\n" + errorThrown);
            }
    });
}
function getcontinfo(txadd){
    console.log({tx:txadd});
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $.ajax({
        type : "POST",
            data:JSON.stringify({tx:txadd}),
            contentType:'application/json',
            url : './getcnt',
            dataType : "json",
            success : function(json) {
                console.log(json);
                if(json.result == true){
                    $("#ethinfo #cntadd td").text(json.data.cntadd);
                    $("#ethinfo #makeradd td").text(json.data.makeradd);
                    $("#ethinfo #designadd td").text(json.data.designadd);
                    $("#ethinfo #objname td").text(json.data.objname);
                    $("#ethinfo #ghash td").text(json.data.ghash);
                    getcntflag = 1;
                }
            },
            error : function(XMLHttpRequest, textStatus, errorThrown) {
                console.log( textStatus + ":\n" + errorThrown);
            }
    });
}
</script>
@endsection
