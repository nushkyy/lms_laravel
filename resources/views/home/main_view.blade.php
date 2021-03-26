@extends("layout.main")

@section('content')

    <div class="row"  style="margin-left:10px;margin-right:10px;">
        <div class="col-sm-2">
            <img src="@if(!empty($profileData[0]['profilepic'])){{$profileData[0]['profilepic']}}@else{{config("app.defaultprofilepic")}}@endif" alt="" style="width:100%;height:auto;" />

            <br/><br/>
            <form action="{{route('uploadpic')}}" method="post" enctype="multipart/form-data" name="form">
                {{csrf_field()}}
              <span class="btn btn-info btn-file" style="height:30px;font-size:10px;;width:100%;">
                    Update Profile Picture <input type="file" name="profilepic" onchange="document.forms.form.submit()">
                </span>
            </form><br/>
            <a href="#" data-toggle='modal' data-target='#update_profile' ><span class="btn btn-primary" style="height:30px;font-size:10px;width:100%;">Edit My Profile </span></a>
            <br/><br/>
            <ul class="list-group">
                <a href="#" data-toggle='modal' data-target='#notif_list' ><li class="list-group-item"> View Notifications</li></a>
                <a href="#" data-toggle='modal' data-target='#assignment_list' ><li class="list-group-item">Assignments ({{count($assignments)}})</li></a>
                <a href="#" data-toggle='modal' data-target='#classes_list' ><li class="list-group-item">Classes ({{count($classData)}})</li></a>
                <a href="#" onclick="updateSeen();" data-toggle='modal' data-target='#inbox_list' ><li class="list-group-item">Inbox ({{count($messages)}})</li></a>

            </ul>


        </div>
        <div class="col-sm-7">
            <form action="{{route('post_wall')}}" method="post" enctype="multipart/form-data">
                {{csrf_field()}}
                <div class="form-group">
                    <textarea type="text" class="form-control" id="content" name="content" style="resize:none;"></textarea><br/>
                    <p><a style="text-decoration:none;cursor:pointer;color: rgb(107, 101, 101); font-size: 12px;">

                            <div id="checkin" style="display:none;" >
                                <input  id="checkinplace" onkeyup="$('#place').html(this.value);" name="checkin" class="form-control" type="text" style="width:100%;" placeholder="Example : <?php // echo randomcheckin(); ?>">
                                <span  onclick="this.style.color='rgb(222, 12, 12)';" class=" glyphicon glyphicon-map-marker"></span> <span id="place"></span></a>
                </div>

                </p>
                <span class="btn btn-success btn-file" style="height:30px;font-size:10px;">
        Add Image <input type="file" name="file">
    </span> &nbsp;<span onclick='$("#checkin").toggle();' class="btn btn-danger btn-file" style="height:30px;font-size:10px;">Check In</span> <br/>
                <button style="float:right;" type="submit" class="btn btn-primary">Post</button><br/><br/>

        </form>
        </div>
        <br/>



        <ul class="nav nav-tabs">
            <li class="active"><a data-toggle="tab" onclick="openedTab=1;" href="#home">Posts <div id="bubble1" style="display:none;background-color: rgb(221, 49, 43); width: 10px; height: 10px; border-radius: 100%; float: right; margin: 6px;">.</div></a></li>
            <li><a data-toggle="tab"  onclick="openedTab=2;"  href="#menu1">Class Updates <div id="bubble2" style="display:none;background-color: rgb(221, 49, 43); width: 10px; height: 10px; border-radius: 100%; float: right; margin: 6px;">.</div></a></li>
            <li><a data-toggle="tab"  onclick="openedTab=3;"  href="#menu2">Bookmarks </a></li>
            @if($userData['userType']==1)
                <li><a data-toggle="tab" onclick="$('#mymap').css('width','100%');$('#mymap').css('height','400px');openedTab=4" href="#menu3">Foot Print</a></li>
            @endif

        </ul>

        <div class="tab-content">
            <div id="home" class="tab-pane fade in active">


                <div id="posts">
                    <?php // include("data/getposts.php"); echo $posts;?>
                </div>

                <div id="loading"><button onclick="loadMore();" style="width:100%;" type="submit" class="btn btn-primary">Load More</button></div>

            </div>
            <div id="menu1" class="tab-pane fade">
                <div id="classposts">
                    <?php // include("data/getclassposts.php"); echo $posts;?>
                </div>
                <div id="loading2"><button onclick="loadMore2();" style="width:100%;" type="submit" class="btn btn-primary">Load More</button></div>
            </div>

            <div id="menu2" class="tab-pane fade">

                <form action="{{route('add_bookmark')}}" method="post">
                    {{csrf_field()}}
                    <input  id="url" name="url" class="form-control" type="text" style="width:100%;" placeholder="Type Website URL">
                    <button style="width:100%;" type="submit" class="btn btn-info">Add</button><br/><br/>
                </form>

                <div id="bookmarks">
                    <?php //
                    //include("data/getbookmarks.php");
                    ?>

                </div>
                <div id="loading3"><button onclick="loadMore3();" style="width:100%;" type="submit" class="btn btn-primary">Load More</button></div>

            </div>
                @if($userData['userType']==1)
                    <div id="menu3" class="tab-pane fade">
                        <div class="col-sm-8">
                            <div id="mymap" style="width: 100% !important;height: 400px !important;"></div>
                        </div>
                        <div class="col-sm-4">
                            <div class="list-group btn-primary">
                                <a href="#" class="list-group-item active">Latest Locations Today</a>
                                @foreach($locations as $row)
                                    <a class="list-group-item list-group-item-primary"
                                       href="#">
                                        <span style="">{{$row['location']."@".$row['time']}}
                            </span>
                                    </a>
                                @endforeach
                            </div>

                        </div>

                    </div>
                @endif

        </div>






    </div>
    <div class="col-sm-3">
        <div class="list-group btn-primary">
            <a href="#" class="list-group-item active">People You May Know</a>
            @foreach($randomUsers as $row)
                <a class="list-group-item list-group-item-primary"
                   href="{{url('view_user')}}/{{$row['id']}}">
                    <img src="{{url($row['profilepic'])}}" style="width:25px;height:25px;float:left;"/>
                    <span style="padding:8px;margin-left:5px;">{{$row['userName']}}
                    </span>
                </a>
            @endforeach
        </div>
        <br/><br/>
        <div class="list-group btn-primary">
            <a href="#" class="list-group-item active">Things near you <button type="button" class="btn btn-info pull-right" onclick="$('#nearbyPlaces').toggle();$('#nearbyPlaces2').toggle();"><i class="fa fa-recycle"></i> </button> </a>
            <span id="nearbyPlaces2">
                <a class="list-group-item list-group-item-primary" onclick="getPlaces('school');" style="cursor:pointer">
                    <span style="padding:8px;margin-left:5px;">Schools</span>
                </a>
                <a class="list-group-item list-group-item-primary" onclick="getPlaces('museum');" style="cursor:pointer">
                    <span style="padding:8px;margin-left:5px;">Museum</span>
                </a>
                <a class="list-group-item list-group-item-primary" onclick="getPlaces('restaurant');" style="cursor:pointer">
                    <span style="padding:8px;margin-left:5px;">Restaurants</span>
                </a>
                <a class="list-group-item list-group-item-primary" onclick="getPlaces('library');" style="cursor:pointer">
                    <span style="padding:8px;margin-left:5px;">Library</span>
                </a>
                <a class="list-group-item list-group-item-primary" onclick="getPlaces('hospital');" style="cursor:pointer">
                    <span style="padding:8px;margin-left:5px;">Hospitals</span>
                </a>
                <a class="list-group-item list-group-item-primary" onclick="getPlaces('train_station');" style="cursor:pointer">
                    <span style="padding:8px;margin-left:5px;">Train Station</span>
                </a>
                <a class="list-group-item list-group-item-primary" onclick="getPlaces('supermarket');" style="cursor:pointer">
                    <span style="padding:8px;margin-left:5px;">Supermarket</span>
                </a>
                <a class="list-group-item list-group-item-primary" onclick="getPlaces('cafe');" style="cursor:pointer">
                    <span style="padding:8px;margin-left:5px;">Cafe</span>
                </a>
                <a class="list-group-item list-group-item-primary" onclick="getPlaces('gym');" style="cursor:pointer">
                    <span style="padding:8px;margin-left:5px;">Gym</span>
                </a>
            </span>
            <span id="nearbyPlaces" style="display:none;">
            </span>
        </div>

    </div>
    <div id="results" style="position: fixed;     bottom: 20px;right:0;">

    </div>
    </div>

    <div id="update_profile" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Update Profile</h4>
                </div>
                <div class="modal-body">
                    <ul class="nav nav-tabs">
                        <li class="active"><a data-toggle="tab" href="#edit_prof">Edit Profile</a></li>
                        <li><a data-toggle="tab" href="#change_pass">Change Password</a></li>
                    </ul>

                    <div class="tab-content">
                        <div id="edit_prof" class="tab-pane fade in active">
                            <form method="post" action="{{route('update_profile')}}">
                                {{csrf_field()}}
                                <br/>
                                <input class="form-control btn-flat" placeholder="Enter Your Full Name" id="fullName" name="fullName" type="text" value="{{$profileData[0]['fullName']}}">
                                <br>
                                <input class="form-control btn-flat" placeholder="Enter Your E-Mail" id="emailAddress" name="emailAddress" type="text" value="{{$profileData[0]['emailAddress']}}">
                                <br>
                                <input class="form-control btn-flat" placeholder="Enter Your Mobile No" id="contactNo" name="contactNo" type="text" value="{{$profileData[0]['contactNo']}}">
                                <br>

                                <textarea class="form-control btn-flat" placeholder="Enter Your Address" id="address" autocomplete="off" style="height:120px" name="address" cols="50" rows="10">{{$profileData[0]['address']}}</textarea>
                                <br>
                                <button type="submit" value="register" class="btn btn-primary  btn-flat width-100">
                                    Edit My Profile</button>

                                <br><br>

                            </form>
                        </div>
                        <div id="change_pass" class="tab-pane fade in">
                            <form method="post" action="{{route('change_password')}}">
                                {{csrf_field()}}
                                <br/>
                                <input class="form-control btn-flat" placeholder="Current Password" id="password1" name="password1" type="password" value="">
                                <br>
                                <input class="form-control btn-flat" placeholder="New Password" id="password2" name="password2" type="password" value="">
                                <br/>
                                <button type="submit" value="changepass" class="btn btn-primary  btn-flat width-100">
                                    Change Password</button>

                                <br><br>

                            </form>
                        </div>


                    </div>

                </div>

            </div>

        </div>
    </div>

    <div id="classes_list" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Joined Classes</h4>
                </div>
                <div class="modal-body">

                    @foreach($classData as $row)
                        <li style="font-size: 17px;" class="list-group-item" id="mem-0">
                            <img src="{{url($row['profilepic'])}}" style="width: 40px; height: 40px; margin-right: 10px;">
                            {{$row['className']}}
                            <a href="{{route('view_class',$row['id'])}}"> <button type="button" class="btn btn-info pull-right" >View</button></a>

                            <div style="clear:both;"></div>
                        </li>
                    @endforeach
                </div>

            </div>

        </div>
    </div>
<div id="notif_list" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Notifications</h4>
                </div>
                <div class="modal-body" style="max-height: 400px;overflow: auto">

                    @foreach($notifications as $row)
                        <li style="font-size: 17px;" class="list-group-item" id="mem-0">
                            {{$row->notification}}
                            <a href="{{route('view_user',$row->opp_id)}}"> <button type="button" class="btn btn-info pull-right" >View</button></a>

                            <div style="clear:both;"></div>
                        </li>
                    @endforeach
                </div>

            </div>

        </div>
    </div>

    <div id="inbox_list" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Inbox</h4>
                </div>
                <div class="modal-body">
                    <p>Messages will be dissapeared once it seen</p>
                    @foreach($messages as $row)
                        <li style="font-size: 17px;" class="list-group-item" id="mem-0">
                            <img src="{{url($row['profilepic'])}}" style="width: 40px; height: 40px; margin-right: 10px;">
                            {{$row['userName']}} : {{$row['message']}}
                            <a href="{{route('view_class',$row['sender'])}}"> <button type="button" class="btn btn-info pull-right" >View User</button></a>

                            <div style="clear:both;"></div>
                        </li>
                    @endforeach
                </div>

            </div>

        </div>
    </div>
 <div id="assignment_list" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Pending Assignments</h4>
                </div>
                <div class="modal-body">

                    @foreach($assignments as $row)
                        <div class="list-group-item list-group-item-primary">
                            <div style="">
                                <input type="hidden" id="assignmentId" value="{{$row['id']}}"/>
                                <div style=""><span class="span1">{{$row['assignmentName']}}</span> </div>
                                <div style="">Description : <span class="span2">{{$row['description']}}</span></div>
                                <div style="">Due Date : <span class="span3">{{date("Y-m-d",strtotime($row['due_date']))}}</span></div>
                                <div style="">
                                    <a href="{{route('view_class',$row['classId'])}}"> <button type="button" class="btn btn-info pull-right" >View Class</button></a>
                                </div>
                                <div style="clear:both;"></div>
                            </div>
                        </div>
                    @endforeach
                </div>

            </div>

        </div>
    </div>

    @push('scripts')
        <script src="http://maps.google.com/maps/api/js?key=AIzaSyD7VjWzVqSOsCIib_hUQ-mv-ry5wzVWTAg"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/gmaps.js/0.4.24/gmaps.js"></script>
        <script>
         @if($userData['userType']==1)
            var locations = <?php print_r(json_encode($locations)) ?>;


            var mymap = new GMaps({

                el: '#mymap',

                lat: 7.8731,

                lng: 80.7718,

                zoom:7

            });


            $.each( locations, function( index, value ){

                mymap.addMarker({

                    lat: value.longitude,

                    lng: value.latitude,

                    title: value.location,

                    click: function(e) {

                        alert('This is '+value.location+'');

                    }

                });

            });

         @endif
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
                                redirect_page("{{route('home',['success'=>'Login successfully'])}}");
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


         function getPlaces(typ) {
             $('#nearbyPlaces').toggle();$('#nearbyPlaces2').toggle();
                     $.ajax({
                         url: "{{route('nearby_places')}}?lonlat="+lat+","+long+'&type='+typ,
                         type: "GET",
                         dataType: 'json',
                         cache: false,
                         success: function(response){
                             var json_obj    =   (response);
                             $("#nearbyPlaces").html('');
                             for(var i =0;i<json_obj.length;i++){

                                 $("#nearbyPlaces").append('<a class="list-group-item list-group-item-primary" style="cursor:pointer">'+
                                     '<span style="padding:8px;margin-left:5px;">' +
                                         '<img alt=" " src="https://maps.googleapis.com/maps/api/place/photo?photoreference='+json_obj[i].photo+'&sensor=false&maxheight=100&maxwidth=100&key=AIzaSyD7VjWzVqSOsCIib_hUQ-mv-ry5wzVWTAg" style="width: 100px;height: auto;">'+
                                 '                              <p>'+json_obj[i].name+'</p>\n' +
                                 '                              <p>'+json_obj[i].address+'</p>\n' +
                                 '                              <p>Rating : '+json_obj[i].rating+'</p>\n' +
                                     '</span>'+
                                     '</a>');
                             }
                         }
                     });
         }

            var load    =   0;
            var load2   =   0;
            var load3   =   0;

            function loadMore() {
                var locc    = $(document).height()-800;
                $("#loading").html('<center><img src="{{url('img/loading.gif')}}" style="width:100px;height:100px;"/></center>');

                $.ajax({
                    type: "POST",
                    dataType: "json",
                    url: "{{route('load_posts')}}",
                    data: {
                        "show":"1",
                        "load":load,
                        "_token":'{{csrf_token()}}'
                    },
                    success: function (msg) {
                        var json_val  =  (msg);

                        if(json_val.code==1){
                            load        =   load+10;
                            if(json_val.posts.length<1){
                                $("#loading").html('<h3>There is no posts to show</h3>');
                                return false;
                            }
                            for(var i =0; i<json_val.posts.length;i++){
                                var content	    =	(json_val.posts[i]["postData"]);
                                var img		    =	(json_val.posts[i]["postImage"]);
                                var postid		=	(json_val.posts[i]["id"]);
                                var location	=	(json_val.posts[i]["postLocation"]);
                                var posttime	=	(json_val.posts[i]["postDateTime"]);
                                var posttimediff=	(json_val.posts[i]["diff"]);
                                var likes		=	(json_val.posts[i]["likes"]);
                                var postBy		=	(json_val.posts[i]["userName"]);
                                var postById	=	(json_val.posts[i]["postBy"]);
                                var postByPic	=	(json_val.posts[i]["profilepic"]);
                                var likeBy	    =	(json_val.posts[i]["likeBy"]);
                                var numlikes    =   (json_val.posts[i]["total_likes"]);

                                if(numlikes=='' || numlikes==null){
                                    numlikes    =   0;
                                }
                                if (postByPic=='' || postByPic==null) {
                                    postByPic		= "{{config("app.defaultprofilepic")}}";
                                }

                                if(location!=""){
                                    location='&nbsp;&nbsp;<span  class=" glyphicon glyphicon-map-marker"></span> '+location;
                                }

                               // var loc='<div class="panel-body">'+location+'</div>';

                                if(likeBy!='' && likeBy!=null){

                                    var liked='<a style="cursor:pointer;" id="likediv-'+postid+'"><span id="likeicon-'+postid+'"  style="color: rgb(222, 12, 12); font-size: 20px;" onclick="unlikePost('+"'"+postid+"',"+numlikes+""+');this.style.color='+"'rgb(107, 101, 101)'"+';" class="glyphicon glyphicon-heart">'+numlikes+'</span></a>';

                                }else{

                                    var liked='<a style="cursor:pointer;" id="likediv-'+postid+'"><span id="likeicon-'+postid+'"  style="color: rgb(107, 101, 101); font-size: 20px;" onclick="likePost('+"'"+postid+"',"+numlikes+""+');'+"'rgb(222, 12, 12)'"+';" class="glyphicon glyphicon-heart">'+numlikes+'</span></a>';

                                }

                                if(img!="" && img!=null){

                                        if(content!=""){content+="<br/><img ondblclick='likePost("+'"'+postid+'",'+numlikes+''+")' style='width:100%;height:auto;' src='"+img+"' alt=''/>";}
                                        if(content==""){content+="<img ondblclick='likePost("+'"'+postid+'",'+numlikes+''+")' style='width:100%;height:auto;' src='"+img+"' alt=''/>";}
                                    

                                }

                                var deletepost="";

                                if(postById=="{{$userData['id']}}")
                                {
                                    deletepost='<a style="cursor:pointer;"><span style="color: rgb(107, 101, 101); ' +
                                        'font-size: 20px;" ' +
                                        'onclick="delPost('+postid+',this);' +
                                    '" class="glyphicon glyphicon-trash"></span></a>';
                                }



                                var posts = '<div class="panel panel-default postdiv" >'+
                                    '<div class="panel-body" style="word-wrap:break-word;">'+
                                    '<div class="col-sm-1" style="padding:1px;height:50px;"><div style="width:100%;height:100%;overflow:hidden;"><img src="'+postByPic+'" style="width:100%;height:auto;border-radius:0%;" /></div></div>'+
                            '<div class="col-sm-8" style="padding:4px;height:70px;" ><b><a style="color:#222;text-decoration:none;" href="view_user/'+postById+'">@'+postBy+'</a></b><a style="cursor:pointer;color: rgb(107, 101, 101); font-size: 12px;">'+location+'</a><br/><a href="{{url('view_posts')}}/'+postid+'"><span class="timepost" style="cursor:pointer;color: rgb(107, 101, 101); font-size: 12px;" title="'+posttime+'">'+convertTime(posttimediff)+'</span></a><br/><br/></div><div class="col-sm-12">'+content+'</div> <br/><br/>'+

                            '<div style="clear:both;"></div><br/>'+liked+''+
                            '&nbsp;&nbsp;&nbsp;'+
                            '<a style="cursor:pointer;" href="{{url('view_posts')}}/'+postid+'"><span style="color: rgb(107, 101, 101); font-size: 20px;" onclick="this.style.color='+"'rgb(222, 160, 12)'"+';" class="glyphicon glyphicon-share"></span></a>'+
                            '&nbsp;&nbsp;&nbsp;'+deletepost +
                            '</div>'+
                            '</div>';

                                $("#posts").append(posts);
                            }
                            $("#loading").html('<button onclick="loadMore();"  style="width:100%;" type="submit" class="btn btn-primary">Load More</button>');


                        }else if(json_val.code==2){
                            var errorMsg    =   "";
                            var resultMsg   =   json_val.result;
                            $("#error_div").html('');

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
                        $("#loading").html('<button onclick="loadMore();"  style="width:100%;" type="submit" class="btn btn-primary">Load More</button>')
                        setTimeout(function () {
                            $("#loading_div").hide();
                        },200);
                    }
                });
            }
            function loadMore3() {
                var locc    = $(document).height()-800;
                $("#loading3").html('<center><img src="{{url('img/loading.gif')}}" style="width:100px;height:100px;"/></center>');

                $.ajax({
                    type: "POST",
                    dataType: "json",
                    url: "{{route('get_bookmarks')}}",
                    data: {
                        "show":"1",
                        "load":load3,
                        "_token":'{{csrf_token()}}'
                    },
                    success: function (msg) {
                        var json_val  =  (msg);

                        if(json_val.code==1){
                            load3        =   load3+10;
                            if(json_val.posts.length<1){
                                $("#loading3").html('<h3>There is no posts to show</h3>');
                                return false;
                            }
                            for(var i =0; i<json_val.posts.length;i++){
                                var content	    =	(json_val.posts[i]["content"]);
                                var url		    =	(json_val.posts[i]["url"]);

                                var posts = '<div class="panel panel-default postdiv" >'+
                                    '<div class="panel-body" style="word-wrap:break-word;">'+
                                    ''+
                            '<div class="col-sm-12">'+content+'</div> <br/><br/>'+
                            '<div class="col-sm-12"><a href="'+url+'" target="_blank">Read More</a></div> <br/><br/>'+
                            '</div>'+
                            '</div>';

                                $("#bookmarks").append(posts);
                            }
                            $("#loading3").html('<button onclick="loadMore3();"  style="width:100%;" type="submit" class="btn btn-primary">Load More</button>');


                        }else if(json_val.code==2){
                            var errorMsg    =   "";
                            var resultMsg   =   json_val.result;
                            $("#error_div").html('');

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
                        $("#loading").html('<button onclick="loadMore();"  style="width:100%;" type="submit" class="btn btn-primary">Load More</button>')
                        setTimeout(function () {
                            $("#loading_div").hide();
                        },200);
                    }
                });
            }
            function loadMore2() {
                var locc    = $(document).height()-800;
                $("#loading2").html('<center><img src="{{url('img/loading.gif')}}" style="width:100px;height:100px;"/></center>');

                $.ajax({
                    type: "POST",
                    dataType: "json",
                    url: "{{route('my_class_posts')}}",
                    data: {
                        "show":"1",
                        "load":load2,
                        "_token":'{{csrf_token()}}'
                    },
                    success: function (msg) {
                        var json_val  =  (msg);

                        if(json_val.code==1){
                            load2        =   load2+10;
                            if(json_val.posts.length<1){
                                $("#loading2").html('<h3>There is no posts to show</h3>');
                                return false;
                            }
                            for(var i =0; i<json_val.posts.length;i++){
                                var content	    =	(json_val.posts[i]["postData"]);
                                var img		    =	(json_val.posts[i]["postImage"]);
                                var postid		=	(json_val.posts[i]["id"]);
                                var location	=	(json_val.posts[i]["postLocation"]);
                                var posttime	=	(json_val.posts[i]["postDateTime"]);
                                var posttimediff=	(json_val.posts[i]["diff"]);
                                var likes		=	(json_val.posts[i]["likes"]);
                                var postBy		=	(json_val.posts[i]["userName"]);
                                var postById	=	(json_val.posts[i]["postBy"]);
                                var postByPic	=	(json_val.posts[i]["profilepic"]);
                                var likeBy	    =	(json_val.posts[i]["likeBy"]);
                                var numlikes    =   (json_val.posts[i]["total_likes"]);
                                var className   =   (json_val.posts[i]["className"]);
                                var classId     =   (json_val.posts[i]["classId"]);

                                if(numlikes=='' || numlikes==null){
                                    numlikes    =   0;
                                }
                                if (postByPic=='' || postByPic==null) {
                                    postByPic		= "{{config("app.defaultprofilepic")}}";
                                }

                                if(location!=""){
                                    location='&nbsp;&nbsp;<span  class=" glyphicon glyphicon-map-marker"></span> '+location;
                                }

                               // var loc='<div class="panel-body">'+location+'</div>';

                                if(likeBy!='' && likeBy!=null){

                                    var liked='<a style="cursor:pointer;" id="likediv-'+postid+'"><span id="likeicon-'+postid+'"  style="color: rgb(222, 12, 12); font-size: 20px;" onclick="unlikeClassPost('+"'"+postid+"',"+numlikes+""+');this.style.color='+"'rgb(107, 101, 101)'"+';" class="glyphicon glyphicon-heart">'+numlikes+'</span></a>';

                                }else{

                                    var liked='<a style="cursor:pointer;" id="likediv-'+postid+'"><span id="likeicon-'+postid+'"  style="color: rgb(107, 101, 101); font-size: 20px;" onclick="likeClassPost('+"'"+postid+"',"+numlikes+""+');'+"'rgb(222, 12, 12)'"+';" class="glyphicon glyphicon-heart">'+numlikes+'</span></a>';

                                }

                                if(img!="" && img!=null){

                                        if(content!=""){content+="<br/><img ondblclick='likeClassPost("+'"'+postid+'",'+numlikes+''+")' style='width:100%;height:auto;' src='"+img+"' alt=''/>";}
                                        if(content==""){content+="<img ondblclick='likeClassPost("+'"'+postid+'",'+numlikes+''+")' style='width:100%;height:auto;' src='"+img+"' alt=''/>";}


                                }

                                var deletepost="";

                                if(postById=="{{$userData['id']}}")
                                {
                                    deletepost='<a style="cursor:pointer;"><span style="color: rgb(107, 101, 101); ' +
                                        'font-size: 20px;" ' +
                                        'onclick="delClassPost('+postid+',this);' +
                                    '" class="glyphicon glyphicon-trash"></span></a>';
                                }



                                var posts = '<div class="panel panel-default postdiv" >'+
                                    '<div class="panel-body" style="word-wrap:break-word;">'+
                                    '<div class="col-sm-1" style="padding:1px;height:50px;"><div style="width:100%;height:100%;overflow:hidden;"><img src="'+postByPic+'" style="width:100%;height:auto;border-radius:0%;" /></div></div>'+
                            '<div class="col-sm-8" style="padding:4px;height:70px;" ><b><a style="color:#222;text-decoration:none;" href="{{url('view_class')}}/'+classId+'">@'+className+'</a></b><a style="cursor:pointer;color: rgb(107, 101, 101); font-size: 12px;">'+location+'</a><br/><a href="{{url('view_posts')}}/'+postid+'"><span class="timepost" style="cursor:pointer;color: rgb(107, 101, 101); font-size: 12px;" title="'+posttime+'">'+convertTime(posttimediff)+'</span></a><br/><br/></div><div class="col-sm-12">'+content+'</div> <br/><br/>'+

                            '<div style="clear:both;"></div><br/>'+liked+''+
                            '&nbsp;&nbsp;&nbsp;'+
                            '<a style="cursor:pointer;" href="{{url('view_user')}}/'+postById+'"><span style="color: rgb(107, 101, 101); font-size: 20px;" onclick="this.style.color='+"'rgb(222, 160, 12)'"+';" class="glyphicon glyphicon-share"></span></a>'+
                            '&nbsp;&nbsp;&nbsp;'+deletepost +
                            '</div>'+
                            '</div>';

                                $("#classposts").append(posts);
                            }
                            $("#loading2").html('<button onclick="loadMore2();"  style="width:100%;" type="submit" class="btn btn-primary">Load More</button>');


                        }else if(json_val.code==2){
                            var errorMsg    =   "";
                            var resultMsg   =   json_val.result;
                            $("#error_div").html('');

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
                        $("#loading2").html('<button onclick="loadMore();"  style="width:100%;" type="submit" class="btn btn-primary">Load More</button>')
                        setTimeout(function () {
                            $("#loading_div").hide();
                        },200);
                    }
                });
            }
            var pageLoaded=0;
            var openedTab=1;
            window.onload=function(){
                loadMore();
                loadMore2();
                loadMore3();

                pageLoaded=1;
            }

            $(window).scroll(function() {
                    if($(window).scrollTop() + $(window).height() == $(document).height()) {
                        if(openedTab==1){
                            loadMore();

                        }if(openedTab==2){
                            loadMore2();

                        }
                        if(openedTab==3){
                            loadMore3();

                        }

                    }
                });
        </script>
    @endpush
@endsection