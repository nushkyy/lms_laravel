@extends("layout.main")

@section('content')

    <div class="col-sm-12" style="margin-top:10px;">
        <div class="row">
            <div class="col-sm-4"></div>
            <div class="col-sm-4">

                <div class="login-form">

                    <h3>Login to {{config('app.urlName')}}</h3>
                    <div id="error_div" >
                        @if(!empty($msg))
                            <div class="alert alert-primary">
                                {{$msg}}
                            </div>
                        @endif
                    </div>
                    {{ Form::open(array(null,null,'onsubmit'=>'return false;','id'=>'loginForm')) }}

                        {{Form::text("username","",array("class"=>"form-control btn-flat","placeholder"=>
                        "Enter Your Username"))}}
                        <br/>
                        {{Form::password("password",array("type"=>"password","class"=>"form-control btn-flat"
                        ,"placeholder"=>"Enter Your Password"))}}
                        <br/>
                        {{Form::button("Login",array("type"=>"button","value"=>"login","class"=>"btn
                        btn-primary btn-flat width-100 savebutton"))}}

                        <br/><br/>
                        <a href="{{route("register")}}"> {{Form::button("Register",array("type"=>"button",
                        "value"=>"register","class"=>"btn bg-danger btn-flat width-100 color-white"))}}</a>

                    {{ Form::close() }}
                </div>
            </div>
            <div class="col-sm-4"></div>
        </div>
    </div>

    @push('scripts')

        <script>
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