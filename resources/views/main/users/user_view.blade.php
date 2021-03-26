@extends("layout.main")

@section('content')

    <div class="row" style="margin-left:10px;margin-right:10px;">
        <div class="col-sm-3">
            <img src="../{{$profileData[0]['profilepic']}}" alt="" style="width:100%;height:auto;" /><br/><br/>
            @if($profileData[0]['id']<>$userData['id'])
                <span id="follow">
                    @if(!empty($profileData[0]['follow_id']) && $profileData[0]['follow_id']!=null)
                        <a href="{{route('unfollow_user',$profileData[0]['id'])}}"> <button type="button" class="btn btn-info" >- UnFollow</button></a>
                    @else
                        <a href="{{route('follow_user',$profileData[0]['id'])}}"> <button type="button" class="btn btn-info">+ Follow</button></a>
                    @endif
                </span>
                &nbsp;&nbsp;<button type="button" class="btn btn-info" data-toggle="modal" data-target="#send_message">Send Message</button>

                &nbsp;&nbsp;
                <br/><br/>
                @if($profileData[0]['userType']==3 && (empty($userData['parentId']) ))
                    @if($userData['userType']==1)
                        <a href="{{route('set_parent_id',$profileData[0]['id'])}}">
                            <button type="button" class="btn btn-success">Add Parent</button>
                        </a>
                    @endif
                @endif
                @if($profileData[0]['id']==$userData['parentId'])
                   <h4>This user registered as your parent</h4>
                @endif
                @if($profileData[0]['parentId']==$userData['id'])
                   <h4>This user registered as your child</h4>

                    <span class="dropdown">
                        <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">
                            Parental Reports
                            <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu">
                            <li><a href="{{route('view_user',array($profileData[0]['id']))}}">Attendance Report</a></li>
                            <li><a href="{{route('view_user',array($profileData[0]['id']))}}">Location Reports</a></li>

                        </ul>
                    </span>

                @endif
                <br/><br/>
            @endif
            <ul class="list-group">
                <li class="list-group-item">Age - {{(int)$profileData[0]['age']}}</li>
                <li class="list-group-item">About - {{$profileData[0]['about']}}</li>
                <li class="list-group-item">Last Seen - {{$profileData[0]['last_activity']}}</li>
                <li class="list-group-item">Address - {{$profileData[0]['address']}}</li>

            </ul>

            <br/>



        </div>  <div id="results" style="position: fixed;     bottom: 20px;right:0;z-index:99999;">

        </div>
        <div class="col-sm-7">

            <ul class="nav nav-tabs">
                <li class="active"><a data-toggle="tab" onclick="openedTab=1"  href="#home">Posts</a></li>
                <li><a data-toggle="tab" onclick="openedTab=2" href="#menu1">Classes</a></li>
                <li><a data-toggle="tab" onclick="openedTab=3" href="#menu2">Followers</a></li>
                <li><a data-toggle="tab" onclick="openedTab=5" href="#menu3">Following</a></li>
                <li><a data-toggle="tab" onclick="openedTab=4" href="#menu4">Bookmarks</a></li>
                @if($profileData[0]['parentId']==$userData['id'])
                    <li><a data-toggle="tab" onclick="$('#mymap').css('width','100%');$('#mymap').css('height','400px');openedTab=5" href="#menu5">Foot Prints</a></li>
                    <li><a data-toggle="tab" onclick="openedTab=5" href="#menu6">Grades</a></li>
                @endif
            </ul>

            <div class="tab-content">
                <div id="home" class="tab-pane fade in active">
                    <div id="posts">

                    </div>
                    <div id="loading"><button onclick="loadMore();" style="width:100%;" type="submit" class="btn btn-primary">Load More</button></div>
                </div>
                <div id="menu1" class="tab-pane fade">
                    <h3>Classes</h3>
                    @foreach($joinedClasses as $row)
                        <li style="font-size: 17px;" class="list-group-item" id="mem-0">
                            <img src="{{url($row['profilepic'])}}" style="width: 40px; height: 40px; margin-right: 10px;">
                            {{$row['className']}}
                                <a href="{{route('view_class',$row['id'])}}"> <button type="button" class="btn btn-info pull-right" >View</button></a>

                            <div style="clear:both;"></div>
                        </li>
                    @endforeach
                </div>
                <div id="menu2" class="tab-pane fade">
                    <h3>Followers</h3>
                    @foreach($followedby as $row)
                        <li style="font-size: 17px;" class="list-group-item" id="mem-0">
                            <img src="{{url($row['profilepic'])}}" style="width: 40px; height: 40px; margin-right: 10px;">
                            {{$row['userName']}}
                            <a href="{{route('view_user',$row['id'])}}"> <button type="button" class="btn btn-info pull-right" >View</button></a>

                            <div style="clear:both;"></div>
                        </li>
                    @endforeach
                </div>
                <div id="menu3" class="tab-pane fade">
                    <h3>Following</h3>
                    @foreach($followings as $row)
                        <li style="font-size: 17px;" class="list-group-item" id="mem-0">
                            <img src="{{url($row['profilepic'])}}" style="width: 40px; height: 40px; margin-right: 10px;">
                            {{$row['userName']}}
                            <a href="{{route('view_user',$row['id'])}}"> <button type="button" class="btn btn-info pull-right" >View</button></a>

                            <div style="clear:both;"></div>
                        </li>
                    @endforeach
                </div>

                <div id="menu4" class="tab-pane fade">
                    <div id="bookmarks">

                    </div>
                    <div id="loading3"><button onclick="loadMore3();" style="width:100%;" type="submit" class="btn btn-primary">Load More</button></div>

                </div>
                    @if($profileData[0]['parentId']==$userData['id'])
                        <div id="menu5" class="tab-pane fade">
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

                    <div id="menu6" class="tab-pane fade">
                        <h3>Grades</h3>

                        @foreach($assignments as $row)
                            <div class="list-group-item list-group-item-primary">
                                <div style="">
                                    <input type="hidden" id="assignmentId" value="{{$row['id']}}"/>
                                    <div style=""><span class="span1">{{$row['assignmentName']}}</span> </div>
                                    <div style="">Description : <span class="span2">{{$row['description']}}</span></div>
                                    <div style="">Due Date : <span class="span3">{{date("Y-m-d",strtotime($row['due_date']))}}</span></div>
                                    <div style="">Class Name : <span class="span3">{{$row['className']}}</span></div>
                                    <div style="">Grade : <span class="span3">{{$row['grade']}}</span></div>

                                </div>
                            </div>
                        @endforeach
                    </div>
                    @endif
            </div>




        </div>
        <div class="col-sm-2">
            <div class="panel panel-default">
                <div class="panel-heading">Latest Places</div>
                <ul class="list-group">
                    @foreach($postLocations as $row)
                        <li class="list-group-item">{{$row->postLocation}}</li>
                    @endforeach
                </ul>

            </div>
        </div>
    </div>


    <div id="send_message" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">×</button>
                    <h4 class="modal-title">Send Message</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <form id="sendMessage">
                            {{csrf_field()}}
                        <input type="hidden" name="msgto" value="{{$profileData[0]['id']}}"/>
                        <textarea placeholder="Type Your Message Here" type="text" class="form-control" id="message"
                                  name="message" style="resize:none;"></textarea><br>
                        </form>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" data-dismiss="modal">Close</button>
                    <button style="float:right;" onclick="sendMessageAjax();" type="submit" class="btn btn-primary">Send</button><br><br>
                </div>
            </div>

        </div>
    </div>

    @push('scripts')

        <script src="http://maps.google.com/maps/api/js?key=AIzaSyD7VjWzVqSOsCIib_hUQ-mv-ry5wzVWTAg"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/gmaps.js/0.4.24/gmaps.js"></script>
        <script>
        @if($profileData[0]['parentId']==$userData['id'])
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
            var load    =   0;
            var load3    =   0;

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
                        "username":"{{ $profileData[0]['id']}}",
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
                                var likes		=	(json_val.posts[i]["likes"]);
                                var postBy		=	(json_val.posts[i]["userName"]);
                                var postById	=	(json_val.posts[i]["postBy"]);
                                var postByPic	=	(json_val.posts[i]["profilepic"]);
                                var likeBy	    =	(json_val.posts[i]["likeBy"]);
                                var numlikes    =   (json_val.posts[i]["total_likes"]);

                                if(location==null){
                                    location    =   "";
                                }
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

                                    var liked='<a style="cursor:pointer;" id="likediv-'+postid+'"><span id="likeicon-'+postid+'"  style="color: rgb(107, 101, 101); font-size: 20px;" onclick="likePost('+"'"+postid+"',"+numlikes+""+');this.style.color='+"'rgb(222, 12, 12)'"+';" class="glyphicon glyphicon-heart">'+numlikes+'</span></a>';

                                }

                                if(img!="" && img!=null){

                                    if(content!=""){content+="<br/><img ondblclick='likePost("+'"'+postid+'",'+numlikes+''+");("+'"#likeicon-'+postid+'"'+")+css("+'"color", "rgb(222, 12, 12)"'+");' style='width:100%;height:auto;' src='../"+img+"' alt=''/>";}
                                    if(content==""){content+="<img ondblclick='likePost("+'"'+postid+'",'+numlikes+''+");("+'"#likeicon-'+postid+'"'+")+css("+'"color", "rgb(222, 12, 12)"'+");' style='width:100%;height:auto;' src='../"+img+"' alt=''/>";}


                                }

                                var deletepost="";

                                var posts = '<div class="panel panel-default postdiv" >'+
                                    '<div class="panel-body" style="word-wrap:break-word;">'+
                                    '<div class="col-sm-1" style="padding:1px;height:50px;"><div style="width:100%;height:100%;overflow:hidden;"><img src="{{url('')}}/'+postByPic+'" style="width:100%;height:auto;border-radius:0%;" /></div></div>'+
                                    '<div class="col-sm-8" style="padding:4px;height:70px;" ><b><a style="color:#222;text-decoration:none;" href="{{url('view_user')}}/'+postById+'">@'+postBy+'</a></b><a style="cursor:pointer;color: rgb(107, 101, 101); font-size: 12px;">'+location+'</a><br/><a href="{{url('view_posts')}}/'+postid+'"><span class="timepost" style="cursor:pointer;color: rgb(107, 101, 101); font-size: 12px;">'+posttime+'</span></a><br/><br/></div><div class="col-sm-12">'+content+'</div> <br/><br/>'+

                                    '<div style="clear:both;"></div><br/>'+liked+''+
                                    '&nbsp;&nbsp;&nbsp;'+
                                    '<a style="cursor:pointer;" href="{{url('view_posts')}}/'+postid+'"><span style="color: rgb(107, 101, 101); font-size: 20px;" onclick="this.style.color='+"'rgb(222, 160, 12)'"+';" class="glyphicon glyphicon-share"></span></a>'+
                                    '&nbsp;&nbsp;&nbsp;'+
                                    deletepost+
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
                    "username":"{{ $profileData[0]['id']}}",
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

            var pageLoaded=0;
            var openedTab=1;
            window.onload=function(){
                loadMore();
                loadMore3();
                pageLoaded=1;
            }

            $(window).scroll(function() {
                if($(window).scrollTop() + $(window).height() == $(document).height()) {
                    if(openedTab==1){
                        loadMore();

                    }

                    if(openedTab==4){
                        loadMore3();

                    }
                }
            });
        </script>
    @endpush
@endsection