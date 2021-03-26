<?php

namespace App\Http\Controllers\core;

use App\Models\core\Bookmark_model;
use App\Models\core\Comments_model;
use App\Models\core\Follow_model;
use App\Models\core\Likes_model;
use App\Models\core\Posts_model;
use App\Models\core\User_model;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Sunra\PhpSimple\HtmlDomParser;


class Posts_controller extends Controller
{
    //
    public function post_wall(Request $request){
        $postObj        =   new Posts_model();

        $userData       =    session()->get('user_data');
        $postMessage    =   $request->input('content');
        $postLocation   =   $request->input('checkin');

        $fileName   =   $this->fileUpload($request,'file');

        if(empty($fileName) && empty($postMessage)){
            return redirect()->route('user_home',['success'=>'Cannot Save Empty Post']);
        }

        if($postMessage==null){$postMessage="";}

        $data           =   array(
                                    "postData"=>$postMessage,
                                    "postImage"=>$fileName,
                                    "postDateTime"=>date("Y-m-d H:i:s"),
                                    "postBy"=>$userData['id'],
                                    "postLocation"=>$postLocation,
                                );

        $result         =   $postObj::insert($data);



        return redirect()->route('user_home',['success'=>'Post saved successfully']);

    }
    public function post_bookmark(Request $request){
        $postObj        =   new Bookmark_model();

        $userData       =   session()->get('user_data');
        $url            =   $request->input('url');

        $parse          =   parse_url($url);
        $path           =   "";
        $sitecontent    =   "";
        $path2          =   2;

        if(isset($parse['path'])){
            $path   =   htmlentities($parse['path']);
            $path   =   explode(".",$path);
            $path2  =   count($path);
            $path   =   $path[count($path)-1];
        }
        if($path==".php" OR $path==".html" OR $path==".xhtml" OR $path==".asp" OR $path=="/" OR $path=="" OR ($path2-1)=="0") {

            $objDOM = new HtmlDomParser();

            $dom = $objDOM::file_get_html($url);

            if(strpos($url,"https://")>-1){
                $hst="https://";
            }else{
                $hst="http://";
            }
            $mainurl=$hst.htmlentities($parse['host']);
            $z=0;
            foreach($dom->find('img') as $element){
                $checkDots  =   explode(".",$element->src);

                if(strpos($element->src,"https://")>-1 OR strpos($element->src,"http://")>-1 OR count($checkDots)>2){
                    $sitecontent.= "<img alt='' src='".htmlentities($element->src) . "' style='max-width:100%;max-height:50px;width:auto;'/>";
                }else{
                    $sitecontent.= "<img alt='' src='".$mainurl.htmlentities($element->src) . "' style='max-width:100%;max-height:50px;width:auto;'/>";
                }
                if($z>1){break;}else{$z++;}
            }

            foreach($dom->find('title') as $element){
                $sitecontent.= "<h4>".htmlentities($element->plaintext) . '</h4>';
                break;
            }
            $z=0;
            foreach($dom->find('p') as $element){
                $sitecontent.= "<p>".htmlentities($element->plaintext) . '</p>';
                if($z>1){break;}else{$z++;}
            }


            $data       =   array(
                                    "url"=>$url,
                                    "content"=>$sitecontent,
                                    "postBy"=>$userData['id'],
                                    "create_date"=>date("Y-m-d H:i:s"),
                                );

            $postObj::insert($data);
            return redirect()->route('user_home',['success'=>'Bookmark saved successfully']);

        }else{
            return redirect()->route('user_home',['error'=>'URL Not supported']);

        }



    }


    public function fileUpload( Request $request,$filename) {

        if ($request->hasFile($filename)) {
            $this->validate($request, [
                $filename => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);
            $image = $request->file($filename);
            $name = sha1(uniqid()).'.'.$image->getClientOriginalExtension();
            $destinationPath = public_path('/uploads/posts');
            $image->move($destinationPath, $name);
            return 'uploads/posts/'.$name;
            // return back()->with('success','Image Upload successfully');
        }
    }

    public function get_posts(Request $request){
        $userData       =    session()->get('user_data');
        $loadMin        =   $request->input('load');
        $loadMax        =   $loadMin+10;

        $username       =   $request->input("username");

        $postObj        =   new Posts_model();


        $result         =   $postObj::select(\DB::raw('tbl_posts.*,tbl_likes.likeBy,users.userName,users.profilepic,
                                                        TIMESTAMPDIFF(SECOND, postDateTime, NOW()) AS "diff",total_post_likes.total_likes'))
                                    ->leftJoin('tbl_likes',function ($join) use($userData){
                                        $userid =   (string)$userData['id'];
                                        $join->on('tbl_likes.postId','=','tbl_posts.id');
                                        $join->on('tbl_likes.likeBy','=',\DB::raw($userid));
                                    })
                                    ->leftJoin('total_post_likes','total_post_likes.postIdLikes','=','tbl_posts.id')
                                    ->join('users','users.id','=','tbl_posts.postBy');


        if(!empty($username) && $username<>null){
            $result->where("postBy",$username);

        }else{
            $result->leftJoin('tbl_follow',function ($join) use($userData){
                $userid =   (string)$userData['id'];
                $join->on('tbl_follow.follow','=','tbl_posts.postBy');
                $join->on('tbl_follow.followby','=',\DB::raw($userid));
            });
            $result->where("postBy",$userData['id']);
            $result->orWhereNotNull("tbl_follow.id","","IS NOT NULL");
        }

            $result     =   $result->limit(10)
                                    ->offset($loadMin)
                                    ->orderBy("postDateTime","desc")
                                    ->get()->toArray();

        $response       =   array("code"=>"1","posts"=>$result);
        return json_encode($response);
    }

    public function get_bookmarks(Request $request){
        $userData       =    session()->get('user_data');
        $loadMin        =   $request->input('load');
        $loadMax        =   $loadMin+10;

        $username       =   $request->input("username");

        $postObj        =   new Bookmark_model();


        $result         =   $postObj::select(\DB::raw('tbl_bookmarks.*,
                                                        TIMESTAMPDIFF(SECOND, create_date, NOW()) AS "diff"'));

        if(!empty($username) && $username<>null){
            $result->where("postBy",$username);

        }else{
            $result->where("postBy",$userData['id']);
        }

        $result     =   $result->limit(10)
            ->offset($loadMin)
            ->orderBy("create_date","desc")
            ->get()->toArray();

        $response       =   array("code"=>"1","posts"=>$result);
        return json_encode($response);
    }

    public function like_post(Request $request)
    {
        $userData = session()->get('user_data');
        $id     =   $request->input("id");
        $likeObj = new Likes_model();

        $validateLike = $likeObj::select('*')->where('likeBy', $userData['id'])->where('postId', $id)->get()
            ->toArray();

        if (count($validateLike) > 0) {
            echo json_encode(array("code"=>0,"result"=>"You are already liked this post"));
        } else {

            $data = array(
                "likeBy" => $userData['id'],
                "postId" => $id,
                "likeDateTime" => date("Y-m-d H:i:s")
            );
            $likeObj::insert($data);
            echo json_encode(array("code"=>1,"result"=>"Successfully Liked the post"));
        }
    }

    public function unlike_post(Request $request){
        $userData               =    session()->get('user_data');
        $id     =   $request->input("id");
        $likeObj  =   new Likes_model();

        $validateLike =   $likeObj::select('*')->where('likeBy',$userData['id'])->where('postId',$id)->get()
            ->toArray();

        if(count($validateLike)>0){


            $likeObj::where('likeBy',$userData['id'])->where('postId',$id)->delete();

            echo json_encode(array("code"=>1,"result"=>"Successfully UnLiked the post"));
        }else{

            echo json_encode(array("code"=>0,"result"=>"You are not liked this post"));
        }
    }
    public function delete_post(Request $request){
        $userData               =    session()->get('user_data');
        $id     =   $request->input("id");
        $postObj  =   new Posts_model();

        $validatePost =   $postObj::select('*')->where('postBy',$userData['id'])->where('id',$id)->get()
            ->toArray();

        if(count($validatePost)>0){


            $postObj::where('postBy',$userData['id'])->where('id',$id)->delete();

            echo json_encode(array("code"=>1,"result"=>"Successfully deleted the post"));
        }else{

            echo json_encode(array("code"=>0,"result"=>"There is no post to delete"));
        }
    }

    public function view_posts($id){
        $data['userData']   =    session()->get('user_data');
        $userData           =   $data['userData'];
        $postObj              =   new Posts_model();
        $data['postData']    = $postObj::select(\DB::raw('tbl_posts.*,users.userName,users.profilepic,tbl_likes.id AS "lk",
                                                        TIMESTAMPDIFF(SECOND, tbl_posts.postDateTime, NOW()) AS "diff",all_likes_by_users.liked_by'))
                                                        ->leftJoin('all_likes_by_users','all_likes_by_users.postId','=','tbl_posts.id')
                                                        ->join('users','users.id','=','tbl_posts.postBy')
                                                        ->leftJoin('tbl_likes',function ($join) use($userData){
                                                            $userid =   (string)$userData['id'];
                                                            $join->on('tbl_likes.postId','=','tbl_posts.id');
                                                            $join->on('tbl_likes.likeBy','=',\DB::raw($userid));
                                                        })
                                                        ->where("tbl_posts.id",$id)
                                                        ->get()->toArray();
        if(!empty($data['postData'][0]['liked_by'])){
            $data['postData'][0]['liked_by']    =   $data['postData'][0]['liked_by'].",";
        }
        $data['all_likes']      =   explode(",",$data['postData'][0]['liked_by']);
        $commentObj             =   new Comments_model();
        $data['all_comments']   =   $commentObj::select(\DB::raw('tbl_comments.*,users.userName,users.id AS "userId"'))
                                    ->join('users','users.id','=','tbl_comments.commentBy')
                                    ->where('tbl_comments.postId',$id)
                                    ->orderBy('tbl_comments.id',"desc")
                                    ->get()->toArray();

        $data['randomUsers']    =   $this->get_random_users();
        return view("main.posts.view_post",$data);
    }


    public function get_random_users(){
        $data['userData']       =    session()->get('user_data');
        $userData               =    session()->get('user_data');
        $userModel              =   new User_model();
        $randomUsers            = $userModel::select(\DB::raw('users.id,users.userName,users.address,users.userType,
                                                                users.contactNo,users.fullName,users.emailAddress,
                                                                tbl_follow.id AS "follow_id",profilepic'))
            ->leftJoin('tbl_follow',function ($join) use($userData){
                $userid =   (string)$userData['id'];
                $join->on('tbl_follow.follow','=','users.id');
                $join->on('tbl_follow.followby','=',\DB::raw($userid));
            })
            ->where('users.id','<>',$userData['id'])
            ->inRandomOrder()->limit(10)->get()->toArray();

        return $randomUsers;

    }

    public function comment_post($id,Request $request){
        $userData               =    session()->get('user_data');
        $comment    =   $request->input("content");
        if(!empty($comment) || $comment==null){
            $data   =   array(
                                    "comment"=>$comment,
                                    "postId"=>$id,
                                    "commentBy"=>$userData['id'],
                                    "commentDateTime"=>date("Y-m-d H:i:s")
                                );

            $commentObj =   new Comments_model();
            $commentObj::insert($data);

            return redirect()->route('view_posts',[$id,'success'=>'Comment saved successfully']);

        }else{
            return redirect()->route('view_posts',[$id,'error'=>'Comment not saved']);
        }

    }
}
