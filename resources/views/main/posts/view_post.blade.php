@extends("layout.main")

@section('content')

    <div class="row">

        <div class="col-sm-10">
            <div class="panel panel-default postdiv" style="margin-bottom:0px;">
                <div class="panel-body" style="word-wrap:break-word;">
                    <div class="col-sm-1" style="padding:1px;height:50px;">
                        <div style="width:100%;height:100%;overflow:hidden;">
                            <img src="{{url($postData[0]['profilepic'])}}" style="width:100%;height:auto;border-radius:0%;">
                        </div>
                    </div>

                    <div class="col-sm-8" style="padding:4px;height:70px;"><b>
                            <a style="color:#222;text-decoration:none;" href="{{url('view_user')}}/{{$postData[0]['postBy']}}">{{"@".$postData[0]['userName']}}</a></b>
                            <a style="cursor:pointer;color: rgb(107, 101, 101); font-size: 12px;"></a><br>
                            <span class="timepost" style="cursor:pointer;color: rgb(107, 101, 101); font-size: 12px;">
                                </span><br><br>
                    </div>
                    <div class="col-sm-12">
                        @if((($postData[0]['postImage'])!='' && ($postData[0]['postImage'])!=null))
                            <img ondblclick="likePost({{$postData[0]['id']}});" style="width:100%;height:auto;"
                                 src="{{url($postData[0]['postImage'])}}" alt="">
                        @endif
                        {{$postData[0]['postData']}}
                    </div>
                    <br><br>

                    <div style="clear:both;"></div>
                    <br>
                    @if($postData[0]['lk']!=null)
                    <a style="cursor:pointer;" id="likediv-{{$postData[0]['id']}}"><span id="likeicon-{{$postData[0]['id']}}"
                                                                         style="color: rgb(222, 12, 12); font-size: 20px;"
                                                                         onclick="unlikePost('{{$postData[0]['id']}}',1)"
                                                                         class="glyphicon glyphicon-heart">{{count($all_likes)-1}}</span></a>
                    @else
                        <a style="cursor:pointer;" id="likediv-{{$postData[0]['id']}}"><span id="likeicon-{{$postData[0]['id']}}"
                                                                                             style="color: rgb(107, 101, 101); font-size: 20px;"
                                                                                             onclick="likePost('{{$postData[0]['id']}}',1)"
                                                                                             class="glyphicon glyphicon-heart">{{count($all_likes)-1}}</span></a>

                    @endif
                    &nbsp;&nbsp;&nbsp;

                    <a style="cursor:pointer;"><span style="color: rgb(107, 101, 101); font-size: 20px;"
                                                     onclick="delPost('16');$(this).closest('.postdiv').fadeOut(1000);this.style.color='rgb(222, 160, 12)';"
                                                     class="glyphicon glyphicon-trash"></span></a>
                </div>
            </div>
            <div style="border: 1px solid #dddddd;width:100%;padding:10px;background-color: #ffffff;">
                @php
                    $total  =   count($all_likes);
                    if($total>5){
                        $show_likes =   5;
                    }else{
                        $show_likes =   $total;
                    }
                @endphp
                @for($i=0;$i<$show_likes;$i++)

                    @if(empty($all_likes[$i]))
                        @continue
                    @endif
                @php
                    $like_row   =   explode("#",$all_likes[$i]);
                @endphp

                       <a href="{{url('view_user')}}/{{$like_row[1]}}"><b style="color:rgb(0, 98, 131);">{{$like_row[0]}}</b></a>,
                @endfor

                @if(count($all_likes)>5)
                    and <a data-toggle='modal' data-target='#all_likes_modal' style='cursor:pointer;'><b style='color:rgb(0, 98, 131);'>{{count($all_likes)-6}} </b></a> others
                @endif
                    likes this
            </div>
            <div style="border: 1px solid #dddddd;width:100%;padding:10px;background-color: #ffffff;">

                <div class="form-group">
                    <form action="{{route('comment',$postData[0]['id'])}}" method="post">
                        {{csrf_field()}}
                        <textarea type="text" class="form-control" id="content" name="content"
                                  style="resize:none;" required></textarea><br>

                        <button style="float:right;" type="submit" class="btn btn-primary">Comment</button>
                        <br><br></form>
                </div>

                @foreach($all_comments as $row)
                    <div style="word-break: break-all;">
                        <a href="{{route('view_user',$row['userId'])}}"><b>{{$row['userName']}} </b></a> : {{$row['comment']}}
                        <p style="clear:both;float:right;"></p>
                        <hr>
                    </div>
                 @endforeach

            </div>


        </div>
    </div>

    <div id="all_likes_modal" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">All Likes</h4>
                </div>
                <div class="modal-body">
                    @for($i=0;$i<$show_likes;$i++)

                        @if(empty($all_likes[$i]))
                            @continue
                        @endif
                        @php
                            $like_row   =   explode("#",$all_likes[$i]);
                        @endphp
                           <a class="list-group-item list-group-item-primary" href="{{url('view_user')}}/{{$like_row[1]}}">{{$like_row[0]}}</a><br/>

                    @endfor
                </div>

            </div>

        </div>
    </div>
    @push('scripts')
        <script>
            window.onload=function(){
                $(".timepost").html(convertTime("{{$postData[0]['diff']}}"));
            }
        </script>
    @endpush
    @endsection

