@extends("layout.main")

    @section('content')
        <nav class="navbar navbar-inverse navbar-fixed-top">
            <div class="container-fluid">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="{{route('home')}}">{{config("app.urlName")}}</a>
                </div>
                <div class="collapse navbar-collapse" id="myNavbar">
                    <ul class="nav navbar-nav">
                        <li><a href="{{route('home')}}">Home</a></li>
                        <li><a href="{{route('register')}}">Register</a></li>
                    </ul>
                </div>
            </div>
        </nav>
        <br/><br/><br/><br/>
        <div class="container">


            <div class="row">

                <div class="col-sm-8">

                    <div class="jumbotron" >
                        <h1 style="font-size:25px;">{{config("app.urlName")}}</h1>
                        <p style="font-size:15px;">Online best place to share your knowledge and be connected with your people who around in studies.</p>
                        <p style="font-size:15px;">Students Don't worry about the studies Students Online will be there for helping u in each step in your studies</p>
                        <p style="font-size:15px;">Lectures don't worry about being connected with your students we are here for you to help</p>
                        <p style="font-size:15px;">Parents don't worry about your child we are here to show their performance in your smart phone or pc</p>
                    </div>

                </div>

                <div class="col-sm-3">

                    <div id="error_div" >
                        @if(!empty($msg))
                            <div class="alert alert-primary">
                                {{$msg}}
                            </div>
                        @endif
                    </div>
                    {{ Form::open(array(null,null,'onsubmit'=>'return false;','id'=>'loginForm')) }}
                        {{ Form::label("username","Username")}}
                        {{Form::text("username","",array("class"=>"form-control btn-flat","placeholder"=>
                            "Enter Your Username"))}}
                        <br/>
                        {{ Form::label("password","Password")}}
                        {{Form::password("password",array("type"=>"password","class"=>"form-control btn-flat"
                            ,"placeholder"=>"Enter Your Password"))}}
                        <br/>
                        {{Form::button("Login",array("type"=>"button","value"=>"login","class"=>"btn
                            btn-primary btn-flat width-100 savebutton"))}}

                        <br/><br/>
                        <a href="{{route("register")}}"> {{Form::button("Register",array("type"=>"button",
                        "value"=>"register","class"=>"btn btn-danger btn-flat width-100 color-white"))}}</a>
                    {{ Form::close() }}
                </div></div></div>
        <div style="clear:both;"></div>
        <div id="section1" class="container-fluid">
            <h1>Students</h1>
            <p>With {{config("app.urlName")}} you can share all the moments happen with your studies, your achievements, fun trips you have visited in you vacation.</p>
            <p>Bookmark your favorite books, articles, websites easily in one click share them with your friends show them what's your interests.</p>
            <p>Don't worry about missing events in your class with {{config("app.urlName")}} you will receive notifications from your lecturer on the spot</p>
            <p>Suffering from writing forms in submitting your assignments. No need to worry we have online assignment submission system #GoGreen <p>
        </div>
        <div id="section2" class="container-fluid">
            <h1>Lectures</h1>
            <p>You can create classes and allow students to join to the class</p>
            <p>You can update your class wall for special notices</p>
            <p>You can send messages to all your students at once</p>
            <p>You can make your students to submit assignments online</p>
        </div>
        <div id="section41" class="container-fluid">
            <h1>Parents</h1>
            <p>Have you ever worried about your child studying far away we are here to break the distance between you and your child</p>
            <p>With our service you will be able to see your child what doing in the studies</p>
            <p>Once your child verified you as their parents then the lecturer can send you instant messages about the child performance also you will be able to see any results which came </p>
        </div>
        </div>
        </div>

        @push('scripts')

            <script>

                
                window.onload=function () {
                    $("#searchForm #date").val(localStorage.getItem("date"));
                    $("#searchForm #location").val(localStorage.getItem("location"));
                    $("#searchForm #num_adults").val(localStorage.getItem("num_adults"));
                    $("#searchForm #num_child").val(localStorage.getItem("num_child"));
                    getCurrentLoc();

                }
                function getCurrentLoc() {
                    if (navigator.geolocation) {
                        navigator.geolocation.getCurrentPosition(showPosition);
                    } else {
                        alert("Geolocation is not supported by this browser.");
                    }
                }

                function showPosition(position) {
                    var lat     =   position.coords.latitude;
                    var long    =   position.coords.longitude;

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

                            $("#location").val(add1+","+add2);

                        });
                }


                $(".savebutton").on("click",function () {
                    $("#loading_div").show();
                    $.ajax({
                        type: "POST",
                        dataType: "json",
                        url: "{{route('login_check')}}",
                        data: $("#loginForm").serialize(),
                        success: function (msg) {
                            var json_val  =  (msg);

                            if(json_val.code==1){
                                show_errors('success',json_val.result);

                                setTimeout(function () {
                                    redirect_page("{{route('user_home',['success'=>'Login successfully'])}}");
                                },1000);
                            }else if(json_val.code==2){
                                var errorMsg    =   "";
                                var resultMsg   =   json_val.result;

                                $("#loginForm input,select,textarea").removeClass("validation-error");
                                $("#error_div").html('');

                                for(var key in resultMsg){
                                    errorMsg    +=  "<p>"+json_val.result[key][0]+"</p>";
                                    $("#"+key).addClass("validation-error");
                                    $("#error_div").append('<span style="color:red"><i class="fa fa-close"></i> ' +
                                        ''+json_val.result[key][0]+'</span><br/>');
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
                            $("#error_div").html('<span style="color:red"><i class="fa fa-close"></i> ' +
                                ''+json_val.result+'</span><br/>');

                            setTimeout(function () {
                                $("#loading_div").hide();
                            },200);
                        }
                    });
                    return false;
                })
            </script>

        @endpush
    @endsection