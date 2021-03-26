@extends("layout.main")

@section('content')

<div style="width:100%;padding:6px;color:white;background-color:#777;"><center><h3>{{$classData[0]['className']}}</h3>
        <p style="font-size: 10px; color: rgb(221, 221, 221);"> - {{$classData[0]['classDescription']}}</p></center>
</div>

<div class="content" style="margin-left:10px;margin-right:10px;">
<div class="row">
    <div class="col-sm-3">
        <img src="{{url($classData[0]['profilepic'])}}" alt="" style="width:100%;height:auto;"><br><br>
            @if($classData[0]['create_by']==$userData['id'])
                <span id="joinclass">
                    <a href="{{route('delete_class',$classData[0]['id'])}}">
                        <button type="button" class="btn btn-danger" >
                        Delete Class</button>
                        </a>
                </span>
                <form action="{{route('update_profilepic',$classData[0]['id'])}}" method="post" enctype="multipart/form-data"
                name="form" style="display:inline;">
                    {{csrf_field()}}
                    <span class="btn btn-info btn-file" style="height:40px;font-size:15px;">
                    Update Picture <input type="file" name="profilepic" onchange="document.forms.form.submit()">
                    </span>
                </form>
            @else
                @if($classData[0]['joinid']!=null && !empty($classData[0]['joinid']) && $classData[0]['joinid']!='null')
                    <a href="{{route('leave_class',$classData[0]['id'])}}"><button type="button" class="btn btn-info" >Leave Class</button></a>
                @else
                    <a href="{{route('join_class',$classData[0]['id'])}}"><button type="button" class="btn btn-info" >Join Class</button></a>
                @endif
        @endif
        <br><br>
        <input type="hidden" id="classidmain" value="1">
        <ul class="list-group">
            <li class="list-group-item">Admin - {{$classData[0]['userName']}}</li>
            <li class="list-group-item">Date Created - {{$classData[0]['create_date']}}</li>
            <li class="list-group-item">Description - {{$classData[0]['classDescription']}}</li>
            <li class="list-group-item">Number of members - {{count($memberList)}}</li>
        </ul>

        <br>

    </div>  <div id="results" style="position: fixed;     bottom: 20px;right:0;z-index:99999;">

    </div>
    <div class="col-sm-7">

        <ul class="nav nav-tabs">
            <li class="active"><a data-toggle="tab" href="#home">Posts</a></li>
            @if($classData[0]['joinid']!=null && !empty($classData[0]['joinid']) && $classData[0]['joinid']!='null')
                <li><a data-toggle="tab" href="#menu1">Members</a></li>
                @if($userData['userType']==1)
                    <li><a data-toggle="tab" href="#menu2">Assignments</a></li>
                @endif
            @endif
            @if($classData[0]['create_by']==$userData['id'])
                <li><a data-toggle="tab" href="#menu1">Members</a></li>
                <li><a data-toggle="tab" href="#menu6">Attendance</a></li>
                <li><a data-toggle="tab" href="#menu4">Submitted Assignments</a></li>
                <li><a data-toggle="tab" href="#menu5">Assignments List</a></li>
            @endif
            @if($userData['userType']==4 && $classData[0]['adminId']==$userData['id'])
                <li><a data-toggle="tab" href="#menu7">Payments</a></li>
            @endif
        </ul>

        <div class="tab-content">

            <div id="home" class="tab-pane fade in active">
                <script>var clsid="{{$classData[0]['id']}}";</script>
                @if($classData[0]['create_by']==$userData['id'])
                <form action="{{route('post_class_wall',$classData[0]['id'])}}" method="post" enctype="multipart/form-data">
                    <div class="form-group">
                        {{csrf_field()}}
                        <textarea type="text" class="form-control" id="content" name="content" style="resize:none;" placeholder="Share your post by typing here!"></textarea><br>
                        <p><a style="text-decoration:none;cursor:pointer;color: rgb(107, 101, 101); font-size: 12px;">
                                <input type="hidden" name="classid" value="1">
                            </a></p><a style="text-decoration:none;cursor:pointer;color: rgb(107, 101, 101); font-size: 12px;">
                              <span class="btn btn-success btn-file" style="height:30px;font-size:10px;">
                            Add Image <input type="file" name="file">
                        </span> <input type="checkbox" name="sendmail" value="1"/> Send Email Notification <br><br>
                            <button style="float:right;" type="submit" class="btn btn-primary">Post</button><br><br>
                        </a></div>
                </form>
                @endif
                <div id="posts">

                </div>
                <div id="loading"><button onclick="loadMore();" style="width:100%;" type="submit" class="btn btn-primary">Load More</button></div>
            </div>
            <div id="menu1" class="tab-pane fade">
                <h3>Members</h3>
                @foreach($memberList as $row)
                    <li style="font-size: 17px;" class="list-group-item" id="mem-0">
                        <img src="{{url($row['profilepic'])}}" style="width: 40px; height: 40px; margin-right: 10px;">
                        {{$row['userName']}}
                        @if($row['followid']!=null && !empty($row['followid']))
                            <a href="{{route('unfollow_user',$row['memberId'])}}"> <button type="button" class="btn btn-info pull-right" >- UnFollow</button></a>
                        @else
                            <a href="{{route('follow_user',$row['memberId'])}}"> <button type="button" class="btn btn-info  pull-right">+ Follow</button></a>
                        @endif
                        @if($classData[0]['create_by']==$userData['id'])
                            @if($classData[0]['adminId']==$row['memberId'])
                                <b>{{"Class Admin"}}</b>
                            @endif
                            <a href="{{route('leave_from_class',array($classData[0]['id'],$row['memberId']))}}">  <button type="button" class="btn btn-danger" style="float:right;" onclick="">+ Remove</button></a>
                            @if($row['userType']==4 && $classData[0]['adminId']==null)
                                <a href="{{route('add_admin',array($classData[0]['id'],$row['memberId']))}}">  <button type="button" class="btn btn-warning" style="float:right;" onclick="">Make Admin</button></a>
                            @endif

                        @endif
                        <div style="clear:both;"></div>
                    </li>
                 @endforeach

            </div>
            <div id="menu6" class="tab-pane fade">
                <h3>Mark Attendance ({{date("Y-m-d")}})</h3>
                <form action="{{route('mark_attendance',$classData[0]['id'])}}" method="post">
                    {{csrf_field()}}
                    <table class="table" style="width:100%">
                        <thead style="background-color: #fff;">
                            <tr>
                                <th>Name</th>
                                <th>Present</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($attendance as $row)
                            <tr>
                                <td><input hidden name="user_id_{{$i}}" value="{{$row->id}}"/> {{$row->userName}}</td>
                                <td><input type="checkbox" name="user_present_{{$i}}" value="1"
                                    @if($row->present!=null && !empty($row->present))
                                        {{"checked"}}
                                    @endif
                                        /></td>
                            </tr>
                           @php $i++ @endphp
                        @endforeach
                        </tbody>
                    </table>
                    <br/>
                    <input type="hidden" name="count" value="{{$i}}"/>
                    <input type="submit" name="submit" value="Save" class="btn btn-info"/>
                </form>


            </div>

            <div id="menu7" class="tab-pane fade">
                <h3>Payments For Month ({{date("Y-m")}})</h3>
                <form action="{{route('mark_payments',$classData[0]['id'])}}" method="post">
                    {{csrf_field()}}
                    <table class="table" style="width:100%">
                        <thead style="background-color: #fff;">
                        <tr>
                            <th>Name</th>
                            <th>Paid</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($payments as $row)
                            <tr>
                                <td><input hidden name="user_id_{{$i}}" value="{{$row->id}}"/> {{$row->userName}}</td>
                                <td><input type="checkbox" name="user_pay_{{$i}}" value="1"
                                    @if($row->paid!=null && !empty($row->paid))
                                        {{"checked"}}
                                            @endif
                                    /></td>
                            </tr>
                            @php $i++ @endphp
                        @endforeach
                        </tbody>
                    </table>
                    <br/>
                    <input type="hidden" name="count" value="{{$i}}"/>
                    <input type="submit" name="submit" value="Save" class="btn btn-info"/>
                </form>


            </div>

            <div id="menu2" class="tab-pane fade">
                <h3>Submit Assignments</h3>

                @foreach($assignments as $row)
                    <div class="list-group-item list-group-item-primary">
                        <div style="">
                            <input type="hidden" id="assignmentId" value="{{$row['id']}}"/>
                            <div style=""><span class="span1">{{$row['assignmentName']}}</span> </div>
                            <div style="">Description : <span class="span2">{{$row['description']}}</span></div>
                            <div style="">Due Date : <span class="span3">{{date("Y-m-d",strtotime($row['due_date']))}}</span></div>
                            <div style="">Grade : <span class="span3">{{$row['grade']}}</span></div>
                            @if($row['due']==1)
                                <h4>Expired Assignment</h4>

                                @else
                                    @if($row['submit']==null || empty($row['submit']))
                                        <form action="{{route('submit_assignment',$row['id'])}}" method="post" enctype="multipart/form-data"
                                              name="form" style="display:inline;">
                                            <input type="hidden" name="classId" value="{{$classData[0]['id']}}"/>
                                            {{csrf_field()}}
                                            <span class="btn btn-info btn-file" style="height:40px;font-size:15px;">
                                                Submit Assignment <input type="file" name="assignment" onchange="document.forms.form.submit()">
                                            </span>
                                        </form>
                                    @else
                                        <h4>Already submitted on {{$row['submit_date']}}</h4>
                                    @endif
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>


            <div id="menu4" class="tab-pane fade">
                <h3>Submitted Assignments</h3>
                @foreach($assignmentssubmit as $row)

                    <div class="list-group-item list-group-item-primary">
                        <a href="#">
                            <img src="{{url($row['profilepic'])}}" style="width:75px;height:75px;float:left;"></a>
                        <div style="margin-left:80px;">
                            <a href="#">
                                <div style="">{{$row['assignmentName']}}</div>
                            </a>
                            <a href="#">
                                <div style="">{{$row['userName']}}</div>
                            </a>

                            <div>Submitted on : {{$row['submit_date']}}</div>
                            <div>Result : {{$row['grade']}}</div>
                            <a href="{{url($row['file'])}}">
                                <button type="button" class="btn btn-info">Download</button>
                            </a>

                            <span class="dropdown">
                                <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">
                                    Update Grades
                                    <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a href="{{route('set_grades',array($row['id'],$row['classId'],'Distinction'))}}">Distinction</a></li>
                                    <li><a href="{{route('set_grades',array($row['id'],$row['classId'],'Merit'))}}">Merit</a></li>
                                    <li><a href="{{route('set_grades',array($row['id'],$row['classId'],'Pass'))}}">Pass</a></li>
                                    <li><a href="{{route('set_grades',array($row['id'],$row['classId'],'Fail'))}}">Fail</a></li>
                                </ul>
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>

            <div id="menu5" class="tab-pane fade">
                <h3>Add / Modify Assignments</h3>
                <a class="btn btn-info btn-flat" data-toggle='modal' data-target='#create_assignment' style='cursor:pointer;'>Create Assignment</a>
                <br/> <br/>
                @foreach($assignments as $row)
                    <div class="list-group-item list-group-item-primary">
                        <div style="">
                            <input type="hidden" id="assignmentId" value="{{$row['id']}}"/>
                            <div style=""><span class="span1">{{$row['assignmentName']}}</span> </div>
                            <div style="">Description : <span class="span2">{{$row['description']}}</span></div>
                            <div style="">Due Date : <span class="span3">{{date("Y-m-d",strtotime($row['due_date']))}}</span></div>
                            <div style="">Payment : <span class="span4">{{$row['payment_required']}}</span></div>
                                <button type="button" onclick="updateAssignment(this);" class="btn btn-info">Update</button>
                            <a href="{{route('deleteassignment',$row['id'])}}">
                                <button type="button" class="btn btn-danger">Delete</button>
                            </a>
                        </div>
                    </div>
                    @endforeach
            </div>

        </div>

    </div>
    <div class="col-sm-2">
        <div class="panel panel-default">
            <div class="panel-heading">You May Like</div>
            <div class="panel-body" style="word-wrap:break-word;">
                <img src="assets/ad2.png"><p style="word-break: break-all;">BCAS Kandy Campus make your future path</p>
            </div>
        </div>
    </div>
</div>
</div>

@push('scripts')

    <script>


        var load    =   0;

        function loadMore() {
            var locc    = $(document).height()-800;
            $("#loading").html('<center><img src="{{url('img/loading.gif')}}" style="width:100px;height:100px;"/></center>');

            $.ajax({
                type: "POST",
                dataType: "json",
                url: "{{route('load_posts_class')}}",
                data: {
                    "show":"1",
                    "load":load,
                    "classId":"{{$classData[0]['id']}}",
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
                            var postBy		=	(json_val.posts[i]["className"]);
                            var postById	=	(json_val.posts[i]["postBy"]);
                            var postByPic	=	(json_val.posts[i]["profilepic"]);
                            var likeBy	    =	(json_val.posts[i]["likeBy"]);
                            var numlikes    =   (json_val.posts[i]["total_likes"]);
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

                                if(content!=""){content+="<br/><img ondblclick='likeClassPost("+'"'+postid+'",'+numlikes+''+")' style='width:100%;height:auto;' src='{{url("")}}/"+img+"' alt=''/>";}
                                if(content==""){content+="<img ondblclick='likeClassPost("+'"'+postid+'",'+numlikes+''+")' style='width:100%;height:auto;' src='{{url("")}}/"+img+"' alt=''/>";}


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
                                '<div class="col-sm-1" style="padding:1px;height:50px;"><div style="width:100%;height:100%;overflow:hidden;"><img src="{{url('')}}/'+postByPic+'" style="width:100%;height:auto;border-radius:0%;" /></div></div>'+
                                '<div class="col-sm-8" style="padding:4px;height:70px;" ><b><a style="color:#222;text-decoration:none;" href="{{url('view_class')}}/'+classId+'">@'+postBy+'</a></b><a style="cursor:pointer;color: rgb(107, 101, 101); font-size: 12px;">'+location+'</a><br/><a href="{{url('view_posts')}}/'+postid+'"><span class="timepost" style="cursor:pointer;color: rgb(107, 101, 101); font-size: 12px;" title="'+posttime+'">'+convertTime(posttimediff)+'</span></a><br/><br/></div><div class="col-sm-12">'+content+'</div> <br/><br/>'+

                                '<div style="clear:both;"></div><br/>'+liked+''+
                                '&nbsp;&nbsp;&nbsp;'+
                                '<a style="cursor:pointer;" href="{{url('view_user')}}/'+postById+'"><span style="color: rgb(107, 101, 101); font-size: 20px;" onclick="this.style.color='+"'rgb(222, 160, 12)'"+';" class="glyphicon glyphicon-share"></span></a>'+
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


        var pageLoaded=0;
        window.onload=function(){
            loadMore();
            pageLoaded=1;
        }

        $(window).scroll(function() {
            if($(window).scrollTop() + $(window).height() == $(document).height()) {
                if(pageLoaded==1){
                    loadMore();

                }
            }
        });

        function updateAssignment(obj) {

            $("#update_assignment").find('form').attr('action',"{{url('update_assignment')}}/"+$(obj).parent('div').find('#assignmentId').val());
            $("#update_assignment").find('#assignmentName').val($(obj).parent('div').find('.span1').html());
            $("#update_assignment").find('#description').val($(obj).parent('div').find('.span2').html());
            $("#update_assignment").find('#due_date').val(($(obj).parent('div').find('.span3').html()));
            $("#update_assignment").find('#payment_required').val(($(obj).parent('div').find('.span4').html()));

            $("#update_assignment").modal('show');
        }
    </script>
@endpush

<div id="create_assignment" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Create a Assignment</h4>
            </div>
            <div class="modal-body">
                <form action="{{route('create_assignment',$classData[0]['id'])}}" method="post">
                    {{csrf_field()}}
                    <div class="form-group">
                        <label for="classname">Assignment Name:</label>
                        <input type="text" class="form-control" name="assignmentName" id="assignmentName" min="3" required>
                    </div>
                    <div class="form-group">
                        <label for="desc">Description</label>
                        <textarea class="form-control" id="description" name="description" min='3' required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="desc">Due Date</label>
                        <input type="date" class="form-control" id="due_date" name="due_date" value="<?php echo date('Y-m-d');?>" required/>
                    </div>


                    <div class="form-group">
                        <label for="desc">Payment</label>
                        <select class="form-control selectize" name="payment_required" id="payment_required">
                            <option value="Payment Not Required">Payment Not Required</option>
                            <option value="Payment Required">Payment Required</option>
                        </select>

                    </div>

                    <button type="submit" class="btn btn-primary">Create</button>

                </form>
            </div>

        </div>

    </div>
</div>

<div id="update_assignment" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Update Assignment</h4>
            </div>
            <div class="modal-body">
                <form action="" method="post">
                    {{csrf_field()}}
                    <div class="form-group">
                        <label for="classname">Assignment Name:</label>
                        <input type="text" class="form-control" name="assignmentName" id="assignmentName" min="3" required>
                    </div>
                    <div class="form-group">
                        <label for="desc">Description</label>
                        <textarea class="form-control" id="description" name="description" min='3' required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="desc">Due Date</label>
                        <input type="date" class="form-control" id="due_date" name="due_date" value="<?php echo date('Y-m-d');?>" required/>
                    </div>
                    <div class="form-group">
                        <label for="desc">Payment</label>
                        <select class="form-control selectize" name="payment_required" id="payment_required">
                            <option value="Payment Not Required">Payment Not Required</option>
                            <option value="Payment Required">Payment Required</option>
                        </select>

                    </div>

                    <button type="submit" class="btn btn-primary">Update</button>

                </form>
            </div>

        </div>

    </div>
</div>
@endsection