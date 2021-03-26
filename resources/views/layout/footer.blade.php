
<div class="loading_div" id="loading_div" style="display: none;position: fixed;top:0;left:0;width: 100%;height:100%">
    <div class="loading-spinner"></div>

    <div class="loading_msg" id="loading_msg_error" style="display: none">
        <div class="alert alert-danger alert-styled-left alert-bordered">
            <button type="button" class="close" data-dismiss="alert"><span>×</span><span class="sr-only">Close</span>
            </button>
            <a href="#" class="alert-link" id="error_msg_loading"></a>
        </div>
    </div>
    <div class="loading_msg" id="loading_msg_success" style="display: none">
        <div class="alert alert-success alert-styled-left alert-bordered">
            <button type="button" class="close" data-dismiss="alert"><span>×</span><span class="sr-only">Close</span>
            </button>
            <a href="#" class="alert-link" id="success_msg_loading"></a>
        </div>
    </div>
</div>
<div id="results" style="position: fixed;     bottom: 20px;right:0;">
    <div id="resultinner" class="alert alert-success" style="display:none">

    </div>
</div>
<div id="errors" style="position: fixed;     bottom: 20px;right:0;">
    <div id="resulterrors" class="alert alert-danger" style="display:none">

    </div>
</div>
{{--
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
--}}
<script src="{{ asset('js/jquery-new.js') }}"></script>
<script src="{{ asset('js/bootstrap.min.js') }}"></script>
<script src="{{ asset('js/script.js') }}"></script>
<script src="{{ asset('js/scripts.js') }}"></script>
<script src="{{ asset('js/jquery-ui.js') }}"></script>
<script src="{{ asset('js/bootstrap-switch.js') }}"></script>

<script>
    window.onload=function(){


    };
    $(document).ready(function(){
        $(".sidebar-menu").css('height',$(document).height()+"px");
        getCurrentLoc();

    })

    var sucess_div_msg  =   '<span style="color:green"><i class="icon-checkmark4"></i> Transaction saved successfully' +
                            '</span><br/>';

    function show_errors(typ,msg) {
        if(typ=="success"){
            $("#loading_div").hide();
            $("#resultinner").fadeIn(1000);
            $("#resultinner").html(msg);
            $("#resultinner").fadeOut(8000);
        }
        if(typ=="error"){
            $("#loading_div").hide();
            $("#resulterrors").fadeIn(1000);
            $("#resulterrors").html(msg);
            $("#resulterrors").fadeOut(8000);
        }
    }

    $("body").on('change click' ,'.validation-error',function () {
        $(this).removeClass('validation-error');
    });

    function show_success_msg(frm) {

        $("#"+frm+" input,select,textarea").removeClass("validation-error");

        $("#error_div").html('');

        $("#"+frm)[0].reset();

        $("#error_div").append(sucess_div_msg);
        $("#error_div").append('<br/>');

        $("#loading_div").hide();
    }

    function dash_board_page() {
        this.location.href  =   "{{route('home')}}";
    }
    function redirect_page(url) {
        this.location.href  =   url;
    }

    function searchNow() {
        var results =   ajaxPost("{{route('search_api')}}","#","searchForm",1);
    }
    function sendMessageAjax() {
        var results =   ajaxPost("{{route('send_message')}}","#","sendMessage",3);
    }
    function setSearches(results) {

        $("#searchlist").html('<li><a href="#">Search Results</a></li>');
        $("#searchlist").append('<li><a href="#"><b>Users List</b></a></li>');
        if(results['usersList'].length<1){
            $("#searchlist").append('<li><a href="#">No results</a></li>');

        }
        for(var i =0;i<results['usersList'].length;i++){

            var html_obj    =   '<li><a href="{{url('view_user')}}/'+results['usersList'][i]['id']+'"><img src="{{url('')}}/'+results['usersList'][i]['profilepic']+'" style="width:25px;height:25px;float:left;"/><span style="padding:8px;margin-left:5px;">'+results['usersList'][i]['userName']+'</span></a></li>';
            $("#searchlist").append(html_obj);
        }
        $("#searchlist").append('<li><a href="#"><b>Class List</b></a></li>');
        if(results['classList'].length<1){
            $("#searchlist").append('<li><a href="#">No results</a></li>');

        }
        for(var i =0;i<results['classList'].length;i++){

            var html_obj    =   '<li><a href="{{url('view_class')}}/'+results['classList'][i]['id']+'"><img src="{{url('')}}/'+results['classList'][i]['profilepic']+'" style="width:25px;height:25px;float:left;"/><span style="padding:8px;margin-left:5px;">'+results['classList'][i]['className']+'</span></a></li>';
            $("#searchlist").append(html_obj);
        }

        $("#loading_div").hide();
    }

    function delPost(id,obj) {
        $("#loading_div").show();
        $.ajax({
            type: "POST",
            dataType: "json",
            url: "{{(route('delete_post'))}}",
            data:{
                "id":id,
                "_token":"{{csrf_token()}}"
            },
            success: function (msg) {
                var json_val  =   (msg);

                if(json_val.code==1){
                    show_errors('success',json_val.result);
                    $(obj).closest('.postdiv').fadeOut(1000);
                    $(obj).css('color','rgb(222, 160, 12)');
                }else if(json_val.code==0){
                    var errorMsg    =   "";
                    var resultMsg   =   json_val.result;

                    show_errors('error',resultMsg);
                }
            }
        });
        return false;
    }
    function likePost(id,nowlikes) {
        $("#loading_div").show();
        $.ajax({
            type: "POST",
            dataType: "json",
            url: "{{(route('like_post'))}}",
            data:{
                "id":id,
                "_token":"{{csrf_token()}}"
            },
            success: function (msg) {
                var json_val  =   (msg);

                if(json_val.code==1){
                    show_errors('success',json_val.result);
                    nowlikes    =   parseFloat($("#likeicon-"+id).html());

                    $("#likeicon-"+id).html(nowlikes+1);
                    $("#likeicon-"+id).attr("onclick","unlikePost('"+id+"',"+nowlikes+");");
                    $("#likeicon-"+id).css("color","rgb(222, 12, 12)");
                }else if(json_val.code==0){
                    var errorMsg    =   "";
                    var resultMsg   =   json_val.result;

                    show_errors('error',resultMsg);
                }
            }
        });
        return false;
    }
    function unlikePost(id,nowlikes) {
        $("#loading_div").show();
        $.ajax({
            type: "POST",
            dataType: "json",
            url: "{{(route('unlike_post'))}}",
            data:{
                "id":id,
                "_token":"{{csrf_token()}}"
            },
            success: function (msg) {
                var json_val  =   (msg);

                if(json_val.code==1){
                    show_errors('success',json_val.result);
                    nowlikes    =   parseFloat($("#likeicon-"+id).html());
                    nowlikes    =   nowlikes-1;
                    $("#likeicon-"+id).html(nowlikes);
                    $("#likeicon-"+id).attr("onclick","likePost('"+id+"',"+nowlikes+");");
                    $("#likeicon-"+id).css("color","rgb(107, 101, 101)");
                }else if(json_val.code==0){
                    var errorMsg    =   "";
                    var resultMsg   =   json_val.result;

                    show_errors('error',resultMsg);
                }
            }
        });
        return false;
    }
    function updateSeen() {
        $.ajax({
            type: "GET",
            dataType: "json",
            url: "{{(route('update_seen'))}}",
            data:{
                "_token":"{{csrf_token()}}"
            },
            success: function (msg) {
            }
        });
        return false;
    }
    function ajaxPost(url,redirect,formName,rt=0) {
        $("#loading_div").show();
        $.ajax({
            type: "POST",
            dataType: "json",
            url: url,
            data: $("#"+formName).serialize(),
            success: function (msg) {
                var json_val  =   (msg);

                    if(rt==1){
                        setSearches(json_val);
                        return true;
                    }

                if(json_val.code==1){
                    show_errors('success',json_val.result);
                    if(rt==0) {
                        setTimeout(function () {
                            redirect_page(redirect);
                        }, 1000);
                    }
                }else if(json_val.code==3){
                    show_errors('error',json_val.result);
                    if(rt==0) {
                        setTimeout(function () {
                            redirect_page(redirect);
                        }, 1000);
                    }
                }else if(json_val.code==2){
                    var errorMsg    =   "";
                    var resultMsg   =   json_val.result;

                    $("#"+formName+" input,select,textarea").removeClass("validation-error");
                    $("#error_div").html('');

                    for(var key in resultMsg){
                        errorMsg    +=  "<p>"+json_val.result[key][0]+"</p>";
                        $("#"+key).addClass("validation-error");
                        $("#error_div").append('<span style="color:red"><i class="fa fa-close">' +
                                                '</i> '+json_val.result[key][0]+'</span><br/>');
                    }
                    $("#error_div").append('<br/>');

                    show_errors('error',errorMsg);
                    setTimeout(function () {
                        $("#loading_div").hide();
                    },200);
                }
            },
            error:function (msg) {
                var json_val  =   JSON.parse(msg.responseText);
                var errorMsg    =   "";
                var resultMsg   =   json_val.result;

                $("#"+formName+" input,select,textarea").removeClass("validation-error");
                $("#error_div").html('');

                for(var key in resultMsg){
                    errorMsg    +=  "<p>"+json_val.result[key][0]+"</p>";
                    $("#"+key).addClass("validation-error");
                    $("#error_div").append('<span style="color:red"><i class="fa fa-close">' +
                                            '</i> '+json_val.result[key][0]+'</span><br/>');
                }
                $("#error_div").append('<br/>');
                setTimeout(function () {
                    $("#loading_div").hide();
                },200);
            }
        });
        return false;
    }



    function delClassPost(id,obj) {
        $("#loading_div").show();
        $.ajax({
            type: "POST",
            dataType: "json",
            url: "{{(route('delete_class_post'))}}",
            data:{
                "id":id,
                "_token":"{{csrf_token()}}"
            },
            success: function (msg) {
                var json_val  =   (msg);

                if(json_val.code==1){
                    show_errors('success',json_val.result);
                    $(obj).closest('.postdiv').fadeOut(1000);
                    $(obj).css('color','rgb(222, 160, 12)');
                }else if(json_val.code==0){
                    var errorMsg    =   "";
                    var resultMsg   =   json_val.result;

                    show_errors('error',resultMsg);
                }
            }
        });
        return false;
    }
    function likeClassPost(id,nowlikes) {
        $("#loading_div").show();
        $.ajax({
            type: "POST",
            dataType: "json",
            url: "{{(route('like_class_post'))}}",
            data:{
                "id":id,
                "_token":"{{csrf_token()}}"
            },
            success: function (msg) {
                var json_val  =   (msg);

                if(json_val.code==1){
                    show_errors('success',json_val.result);
                    nowlikes    =   parseFloat($("#likeicon-"+id).html());

                    $("#likeicon-"+id).html(nowlikes+1);
                    $("#likeicon-"+id).attr("onclick","unlikeClassPost('"+id+"',"+nowlikes+");");
                    $("#likeicon-"+id).css("color","rgb(222, 12, 12)");
                }else if(json_val.code==0){
                    var errorMsg    =   "";
                    var resultMsg   =   json_val.result;

                    show_errors('error',resultMsg);
                }
            }
        });
        return false;
    }
    function unlikeClassPost(id,nowlikes) {
        $("#loading_div").show();
        $.ajax({
            type: "POST",
            dataType: "json",
            url: "{{(route('unlike_class_post'))}}",
            data:{
                "id":id,
                "_token":"{{csrf_token()}}"
            },
            success: function (msg) {
                var json_val  =   (msg);

                if(json_val.code==1){
                    show_errors('success',json_val.result);
                    nowlikes    =   parseFloat($("#likeicon-"+id).html());
                    nowlikes    =   nowlikes-1;
                    $("#likeicon-"+id).html(nowlikes);
                    $("#likeicon-"+id).attr("onclick","likeClassPost('"+id+"',"+nowlikes+");");
                    $("#likeicon-"+id).css("color","rgb(107, 101, 101)");
                }else if(json_val.code==0){
                    var errorMsg    =   "";
                    var resultMsg   =   json_val.result;

                    show_errors('error',resultMsg);
                }
            }
        });
        return false;
    }

    function getCurrentLoc() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(showPosition);
        } else {
            alert("Geolocation is not supported by this browser.");
        }
    }

    var gps_track   =   setInterval(function(){ getCurrentLoc(); }, 60000);
    var last_gps    =   "";
    var lat         =   "";
    var long         =   "";
    var shownb       =   "";
    function showPosition(position) {
         lat     =   position.coords.latitude;
         long    =   position.coords.longitude;

        $.get("https://maps.googleapis.com/maps/api/geocode/json?latlng="+lat+","+long+"" +
            "&sensor=true&key=AIzaSyD7VjWzVqSOsCIib_hUQ-mv-ry5wzVWTAg",
            function(data, status){

                var json_obj    =   (data);

                if(json_obj["results"][0]["address_components"][0]["long_name"]){
                    var add1        =   json_obj["results"][0]["address_components"][0]["long_name"];
                }

                if(json_obj["results"][0]["address_components"][3]["long_name"]){
                    var add2        =   json_obj["results"][3]["address_components"][0]["long_name"];
                }

                var add =   json_obj["results"][0]["formatted_address"];
                if(last_gps==add){
                    return;
                }else{
                    last_gps=add;
                }
                $.ajax({
                    type: "POST",
                    dataType: "json",
                    url: "{{(route('gps_log'))}}",
                    data:{
                        "add":add,
                        "lat":lat,
                        "long":long,
                        "_token":"{{csrf_token()}}"
                    },
                    success: function (msg) {

                    }
                });
            });
    }

    $("body").on('dblclick','p',function () {
        if(this.innerText==''){
            return;
        }
        readMessage(this.innerText);
        preventDefault();
    });

    $("body").on('dblclick','.postdiv div',function () {
        if(this.innerText==''){
            return;
        }
        readMessage(this.innerText);
        preventDefault();
    });

    function readMessage(msgText) {
        var msg = new SpeechSynthesisUtterance();
        var voices = window.speechSynthesis.getVoices();
        msg.voice = voices[5]; // Note: some voices don't support altering params
        msg.voiceURI = 'native';
        msg.volume = 1; // 0 to 1
        msg.rate = 0.8; // 0.1 to 10
        msg.pitch = 1; //0 to 2
        msg.text = msgText;
        msg.lang = 'en-US';

        msg.onend = function(e) {
            //console.log('Finished in ' + event.elapsedTime + ' seconds.');
        };

        speechSynthesis.speak(msg);
    }

</script>

@stack('scripts')

    @if(isset($userData))
        @if(($userData["userType"]=="2"))
            <div id="create_class_modal" class="modal fade" role="dialog">
                <div class="modal-dialog">

                    <!-- Modal content-->
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title">Create a class</h4>
                        </div>
                        <div class="modal-body">
                            <form action="{{route('create_class')}}" method="post">
                                {{csrf_field()}}
                                <div class="form-group">
                                    <label for="classname">Class Name:</label>
                                    <input type="text" class="form-control" name="className" id="className" min="3" required>
                                </div>
                                <div class="form-group">
                                    <label for="desc">Description</label>
                                    <textarea class="form-control" id="classDescription" name="classDescription" min='3' required></textarea>
                                </div>

                                <button type="submit" class="btn btn-primary">Create</button>

                            </form>
                        </div>

                    </div>

                </div>
            </div>
        @endif
    @endif
</body>
</html>