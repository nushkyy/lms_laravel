@extends("layout.main")

@section('content')

    <div class="col-sm-12" style="margin-top:10px;">
        <div class="row">
            <div class="col-sm-4"></div>
            <div class="col-sm-4">
                <div class="login-form">
                    <div id="error_div" >
                        @if(!empty($msg))
                            <div class="alert alert-primary">
                                {{$msg}}
                            </div>
                        @endif
                    </div>
                    <h3>Register to {{config('app.urlName')}}</h3><br/>
                    {{ Form::open(array(null,"id='saveForm'",'onsubmit'=>'return false;')) }}
                        {{Form::text("username","",array("class"=>"form-control btn-flat","placeholder"=>
                        "Enter Your Username","id"=>"username"))}}
                        <br/>
                        {{Form::password("password",array("type"=>"password","class"=>"form-control btn-flat",
                        "placeholder"=>"Enter Your Password","id"=>"password"))}}
                        <br/>
                        {{Form::text("fullName","",array("class"=>"form-control btn-flat","placeholder"=>
                        "Enter Your Full Name","id"=>"fullName"))}}
                        <br/>
                        {{Form::text("emailAddress","",array("class"=>"form-control btn-flat","placeholder"=>
                        "Enter Your E-Mail","id"=>"emailAddress"))}}
                        <br/>
                        {{Form::text("contactNo","",array("class"=>"form-control btn-flat","placeholder"=>
                        "Enter Your Mobile No","id"=>"contactNo"))}}
                        <br/>

                        {{Form::textarea("address","",array("class"=>"form-control btn-flat","placeholder"=>
                        "Enter Your Address","id"=>"address","autocomplete"=>"off","style"=>"height:120px"
                        ))}}
                    <br/>
                        {{Form::select("userType",array("1"=>"Student","2"=>"Leturer","3"=>"Parent","4"=>"Admin"),"",array("class"=>"form-control
                        selectize btn-flat width-100"))}}
                    <br/>{{Form::button("Register",array("type"=>"submit","value"=>"register","class"=>"btn
                    btn-primary savebutton btn-flat width-100"))}}

                        <br/><br/>
                        <a href="{{route("signin")}}"> {{Form::button("Login",array("type"=>"button","value"=>
                        "login","class"=>"btn btn-danger btn-flat width-100 color-white"))}}</a>

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
                    url: "{{route('save_user')}}",
                    data: $("#saveForm").serialize(),
                    success: function (msg) {
                        var json_val  =   (msg);

                        if(json_val.code==1){
                            show_errors('success',json_val.result);

                            setTimeout(function () {
                                redirect_page("{{route('signin',['success'=>'User saved successfully'])}}");
                            },1000);
                        }else if(json_val.code==2){
                            var errorMsg    =   "";
                            var resultMsg   =   json_val.result;

                            $("#saveForm input,select,textarea").removeClass("validation-error");
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

                        show_errors('error',json_val.result);
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