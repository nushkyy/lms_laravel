<nav class="navbar navbar-inverse navbar-fixed-top">
    <div class="container-fluid">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="{{route('user_home')}}">{{config("app.urlName")}}</a>
        </div>
        <div class="collapse navbar-collapse" id="myNavbar">
            <ul class="nav navbar-nav">
                <li><a href="{{route('user_home')}}">Home</a></li>

                @if(isset($userData))
                <form class="navbar-form navbar-left" id="searchForm" role="search" style="width:500px;max-width:100%;">
                    {{csrf_field()}}
                    <div class="form-group" style="width:100%;" id="">
                        <div class="dropdown">
                            <input  id="typeahead" name="search" class="form-control" type="text" data-toggle="dropdown" style="width:100%;" onkeyup="searchNow();"  autocomplete="off" spellcheck="false" >

                            <ul class="dropdown-menu btn-danger" id="searchlist" style="width:100%;background-color:#444;">

                                <li><a href="#">Type to search users and classes</a></li>

                            </ul>

                        </div>
                    </div>
                </form>
                    @if(($userData["userType"]=="2"))
                        <li><a  data-toggle='modal' data-target='#create_class_modal' style='cursor:pointer;'>Create Class</a></li>
                    @endif
                @endif
            </ul>

            <div class="col-sm-2 pull-right">
                <ul class="nav navbar-nav">
                    @if(isset($userData))

                        <li class="dropdown" style="color: #FFF;text-decoration: none;list-style: none;">
                            <a class="dropdown-toggle" data-toggle="dropdown" href="#" style="color:#FFF;font-size:15px"> <span class="caret"></span>
                                {{$userData['fullname']}}
                            </a>
                            <ul class="dropdown-menu">
                                <li><a href="#"><a href="{{route('logout')}}"> <button class="btn btn-danger btn-flat"><i class="fa fa-lock"></i>
                                                Logout</button></a></a></li>
                                <li><a href="#"> @if(($userData["userType"]=="1"))
                                            <a href="{{route('dashboard')}}"> <button class="btn btn-danger btn-flat">
                                                    <i class="fa fa-tachometer-alt"></i> Dashboard</button></a>
                                        @endif</a></li>
                            </ul>
                        </li>
                    @else

                        <a href="{{route('signin')}}"> <button class="btn btn-danger btn-flat">Sign In</button></a>
                    @endif
                </ul>
            </div>
        </div>
    </div>
</nav><br/><br/>

    @if(!empty(app('request')->input('success')))
        <div class="alert alert-success alert-dismissible fade in" style="clear:both">
            <strong>Alert!</strong> {{app('request')->input('success')}}
        </div>
    @endif
    @if(!empty(app('request')->input('error')))
        <div class="alert alert-danger alert-dismissible fade in" style="clear:both">
            <strong>Alert!</strong> {{app('request')->input('error')}}
        </div>
    @endif

<br/>