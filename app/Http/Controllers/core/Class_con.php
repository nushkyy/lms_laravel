<?php

namespace App\Http\Controllers\core;

use App\Http\Controllers\Api\Send_email;
use App\Http\Controllers\AuthCon;
use App\Models\core\Assignment_model;
use App\Models\core\Assignment_submit_model;
use App\Models\core\Attendance_model;
use App\Models\core\Class_members_model;
use App\Models\core\Class_posts_model;
use App\Models\core\Likes_model;
use App\Models\core\Payments_model;
use App\Models\core\User_model;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\core\Class_model;
use Illuminate\Support\Facades\Validator;

class Class_con extends AuthCon
{
    //
    public function create_class(Request $request){
        $userData = session()->get('user_data');
        $rule =   $this->rules();
        $data  =  $request->except(array('_token')) ;
        $validator = Validator::make($data,$rule);

        //if validation fails
        if($validator->fails()){
            $msg    =   json_decode($validator->messages());
            foreach ($msg as $row){

                return redirect()->route('user_home',['error'=>$row[0]]);
            }

        }else{

            $classObj   =   new Class_model();

            $data       =   array(
                                    "className"=>$request->input("className"),
                                    "classDescription"=>$request->input("classDescription"),
                                    "create_by"=>$userData['id'],
                                    "create_date"=>date("Y-m-d H:i:s"),
                                );

            $classObj::insertGetId($data);
            $id =   \DB::getPdo()->lastInsertId();

            $membersObj     =   new Class_members_model();
            $data = array(
                "memberId" => $userData['id'],
                "classId" => $id,
                "join_date" => date("Y-m-d H:i:s")
            );
            $membersObj::insert($data);
            return redirect()->route('view_class',[$id,'success'=>"Successfully Created the Class"]);

        }
    }

    public function view_class($id){

        $data['userData']       =    session()->get('user_data');
        $userData               =    session()->get('user_data');
        $classObj               =   new Class_model();

        $data['classData']      =   $classObj::select(\DB::raw("tbl_class.*,users.userName,tbl_class_members.id AS 'joinid'"))
                                    ->leftJoin('users','tbl_class.create_by','=','users.id')
                                    ->leftJoin('tbl_class_members',function ($join) use($userData){
                                        $userid =   (string)$userData['id'];
                                        $join->on('tbl_class_members.classId','=','tbl_class.id');
                                        $join->on('tbl_class_members.memberId','=',\DB::raw($userid));
                                    })
                                    ->where('tbl_class.id',$id)->get()->toArray();

        $data['memberList']     =   $this->get_class_members($id);

        $assignmentsObj         =   new Assignment_model();

        $data['assignments']   =   $assignmentsObj::select(\DB::raw("tbl_assignments.*,
                                                    tbl_assignments_submit.id AS 'submit',
                                                    tbl_assignments_submit.submit_date,tbl_assignments_submit.grade,
                                                    IF(due_date<DATE(now()),'1','0') AS due"))
                                                    ->leftJoin('tbl_assignments_submit',function ($join) use($userData){
                                                        $userid =   (string)$userData['id'];
                                                        $join->on('tbl_assignments_submit.assignId','=','tbl_assignments.id');
                                                        $join->on('tbl_assignments_submit.postBy','=',\DB::raw($userid));
                                                    })
                                                    ->where("classId",$id)->orderBy('tbl_assignments.due_date')
                                                    ->get()->toArray();
        $submitAssignmentObj    =   new Assignment_submit_model();
        $data['assignmentssubmit']  =   $submitAssignmentObj::select(\DB::raw("tbl_assignments_submit.*,tbl_assignments.assignmentName,tbl_assignments.classId,users.userName,users.profilepic"))
                                                ->leftJoin('users','tbl_assignments_submit.postBy','=','users.id')
                                                ->join('tbl_assignments','tbl_assignments.id','=','tbl_assignments_submit.assignId')
                                                ->where("tbl_assignments.classId",$id)
                                                ->orderBy('tbl_assignments_submit.grade')
                                                ->orderBy('tbl_assignments_submit.id')
                                                ->get()->toArray();

        $data['attendance']         =   \DB::select("call get_attendance(".$id.")");
        $data['payments']          =   \DB::select("call get_payments(".$id.")");

        $data['i']                  =   0;
        return view("main.class.class_view",$data);
    }

    public function get_class_members($id){
        $memberObj              =   new Class_members_model();
        $userData               =   session()->get('user_data');

        $result                 =   $memberObj::select(\DB::raw("tbl_class_members.*,users.userName,users.profilepic,users.userType,
                                                                    users.emailAddress,tbl_follow.id AS 'followid'"))
                                    ->leftJoin('users','users.id','=','tbl_class_members.memberId')
                                    ->leftJoin('tbl_follow',function ($join) use($userData){
                                        $userid =   (string)$userData['id'];
                                        $join->on('tbl_follow.follow','=','tbl_class_members.memberId');
                                        $join->on('tbl_follow.followby','=',\DB::raw($userid));
                                    })
                                    ->where('tbl_class_members.classId',$id)
                                    ->get()->toArray();
        return $result;
    }

    public function postClassWall($id,Request $request){
        $classObj   =   new Class_model();

        $userData       =    session()->get('user_data');
        $postMessage    =   $request->input('content');
        $postLocation   =   $request->input('checkin');

        $validate_class =   $classObj::select('*')->where("id",$id)->where("create_by",$userData['id'])->get()->toArray();

        if($validate_class>0) {
            $fileName = $this->fileUpload2($request, 'file');

            if (empty($fileName) && empty($postMessage)) {
                return redirect()->route('view_class', [$id,'error' => 'Cannot Save Empty Post']);
            }

            if ($postMessage == null) {
                $postMessage = "";
            }

            $data = array(
                "classId" => $id,
                "postData" => $postMessage,
                "postImage" => $fileName,
                "postDateTime" => date("Y-m-d H:i:s"),
                "postBy" => $userData['id'],
                "postLocation" => $postLocation,
            );

            $classPostObj   =   new Class_posts_model();

            $result = $classPostObj::insert($data);

            $sendMailCheck  =   $request->input("sendmail");

            if($sendMailCheck=="1" && $postMessage<>null && !empty($postMessage)){
                $members    =   $this->get_class_members($id);

                $mailObj    =   new Send_email();

                $arraySend  =   array();
                $i          =   0;
                foreach ($members as $row){
                    $arraySend[$i]  =   $row['emailAddress'];
                    $i++;
                }

                $altMsg =   "<br/><br/><a>This Message been posted by class <a href='".url('view_class',$id)."'>".$validate_class[0]['className']."</a></b><br/>";

                $newPost    =   str_replace(PHP_EOL,"<br/>",$postMessage).$altMsg;

                $mailObj->sendMail($arraySend,$validate_class[0]['className']." posted",$newPost);

            }

            return redirect()->route('view_class',[$id,'success'=>"Post created successfully"]);
        }else{
            return redirect()->route('view_class',[$id,'error'=>"Only class owner can crate posts"]);

        }
    }

    public function loadClassPosts(Request $request){
        $userData       =    session()->get('user_data');
        $loadMin        =   $request->input('load');

        $class          =   $request->input("classId");

        $postObj        =   new Class_posts_model();


        $result         =   $postObj::select(\DB::raw('tbl_class_posts.*,tbl_likes.likeBy,users.userName,users.profilepic,tbl_class.className,
                                                        TIMESTAMPDIFF(SECOND, postDateTime, NOW()) AS "diff",total_post_likes.total_likes'))
                                                        ->leftJoin('tbl_likes',function ($join) use($userData){
                                                            $userid =   (string)$userData['id'];
                                                            $join->on('tbl_likes.postClassId','=','tbl_class_posts.id');
                                                            $join->on('tbl_likes.likeBy','=',\DB::raw($userid));
                                                        })
                                                        ->leftJoin('total_post_likes','total_post_likes.postClassId','=','tbl_class_posts.id')
                                                        ->join('users','users.id','=','tbl_class_posts.postBy')
                                                        ->join('tbl_class','tbl_class.id','=','tbl_class_posts.classId')
                                                        ->where("classId",$class);

        $result     =   $result->limit(10)
            ->offset($loadMin)
            ->orderBy("postDateTime","desc")
            ->get()->toArray();

        $response       =   array("code"=>"1","posts"=>$result);
        return json_encode($response);
    }

    public function myClassPosts(Request $request){
        $userData       =    session()->get('user_data');
        $loadMin        =   $request->input('load');

        $postObj        =   new Class_posts_model();


        $result         =   $postObj::select(\DB::raw('tbl_class_posts.*,tbl_likes.likeBy,users.userName,tbl_class.profilepic,tbl_class.className,
                                                        TIMESTAMPDIFF(SECOND, postDateTime, NOW()) AS "diff",total_post_likes.total_likes'))
                                                        ->leftJoin('tbl_likes',function ($join) use($userData){
                                                            $userid =   (string)$userData['id'];
                                                            $join->on('tbl_likes.postClassId','=','tbl_class_posts.id');
                                                            $join->on('tbl_likes.likeBy','=',\DB::raw($userid));
                                                        })
                                                        ->join('tbl_class_members',function ($join) use($userData){
                                                            $userid =   (string)$userData['id'];
                                                            $join->on('tbl_class_members.classId','=','tbl_class_posts.classId');
                                                            $join->on('tbl_class_members.memberId','=',\DB::raw($userid));
                                                        })
                                                        ->leftJoin('total_post_likes','total_post_likes.postClassId','=','tbl_class_posts.id')
                                                        ->join('users','users.id','=','tbl_class_posts.postBy')
                                                        ->join('tbl_class','tbl_class.id','=','tbl_class_posts.classId');

        $result     =   $result->limit(10)
            ->offset($loadMin)
            ->orderBy("postDateTime","desc")
            ->get()->toArray();

        $response       =   array("code"=>"1","posts"=>$result);
        return json_encode($response);
    }

    public function rules($id=null){
        //validation rules
        $classValidate   =   "unique:tbl_class,className";

        if($id!=null){
            $classValidate.= ",".$id;
        }
        return [
            'className' => 'required|'.$classValidate.'|max:50|min:3',
            'classDescription' => 'required|max:100|min:5',
        ];
    }



    public function updateProfilePic($id,Request $request){
        $userData   =    session()->get('user_data');
        $classObj   =   new Class_model();

        $validate_class =   $classObj::select('*')->where("id",$id)->where("create_by",$userData['id'])->get()->toArray();

        if($validate_class>0){
            $result = $this->fileUpload($request,'profilepic',$id);

            if(!empty($result)){

                $data                   =   array("profilepic"=>$result);


                $data['classData']    =   $classObj::where("id",$id)->update($data);

                return redirect()->route('view_class',[$id,'success'=>"Successfully Updated the picture"]);
            }
        }else{
            return redirect()->route('view_class',[$id,'success'=>"This class not created by you"]);
        }
    }


    public function fileUpload2( Request $request,$filename) {

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
    public function fileUpload3( Request $request,$filename) {

        if ($request->hasFile($filename)) {
            $this->validate($request, [
                $filename => 'required|mimes:jpeg,png,jpg,gif,svg,rar,zip,docx,doc,pdf,xlsx,xls,7zip|max:5048',
            ]);
            $image = $request->file($filename);
            $name = sha1(uniqid()).'.'.$image->getClientOriginalExtension();
            $destinationPath = public_path('/uploads/assignments');
            $image->move($destinationPath, $name);
            return 'uploads/assignments/'.$name;
            // return back()->with('success','Image Upload successfully');
        }
    }

    public function fileUpload( Request $request,$filename,$file) {

        $this->validate($request, [
            $filename => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($request->hasFile($filename)) {

            $image = $request->file($filename);
            $name = sha1($file).'.'.$image->getClientOriginalExtension();
            $destinationPath = public_path('/uploads/profilepic/class');
            $image->move($destinationPath, $name);
            return 'uploads/profilepic/class/'.$name;
            // return back()->with('success','Image Upload successfully');
        }
    }


    public function like_post(Request $request)
    {
        $userData = session()->get('user_data');
        $id     =   $request->input("id");
        $likeObj = new Likes_model();

        $validateLike = $likeObj::select('*')->where('likeBy', $userData['id'])->where('postClassId', $id)->get()
            ->toArray();

        if (count($validateLike) > 0) {
            echo json_encode(array("code"=>0,"result"=>"You are already liked this post"));
        } else {

            $data = array(
                "likeBy" => $userData['id'],
                "postClassId" => $id,
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

        $validateLike =   $likeObj::select('*')->where('likeBy',$userData['id'])->where('postClassId',$id)->get()
            ->toArray();

        if(count($validateLike)>0){


            $likeObj::where('likeBy',$userData['id'])->where('postClassId',$id)->delete();

            echo json_encode(array("code"=>1,"result"=>"Successfully UnLiked the post"));
        }else{

            echo json_encode(array("code"=>0,"result"=>"You are not liked this post"));
        }
    }
    public function delete_post(Request $request){
        $userData               =    session()->get('user_data');
        $id     =   $request->input("id");
        $postObj  =   new Class_posts_model();

        $validatePost =   $postObj::select('*')->where('postBy',$userData['id'])->where('id',$id)->get()
            ->toArray();

        if(count($validatePost)>0){


            $postObj::where('postBy',$userData['id'])->where('id',$id)->delete();

            echo json_encode(array("code"=>1,"result"=>"Successfully deleted the post"));
        }else{

            echo json_encode(array("code"=>0,"result"=>"There is no post to delete"));
        }
    }



    public function join_class($id,Request $request)
    {
        $userData = session()->get('user_data');
        $membersObj = new Class_members_model();

        $validateJoin = $membersObj::select('*')->where('memberId', $userData['id'])->where('classId', $id)->get()
            ->toArray();

        if (count($validateJoin) > 0) {
            return redirect()->route('view_class',[$id,'error'=>"You are already joined this class"]);

        } else {

            $data = array(
                "memberId" => $userData['id'],
                "classId" => $id,
                "join_date" => date("Y-m-d H:i:s")
            );
            $membersObj::insert($data);
            return redirect()->route('view_class',[$id,'success'=>"Successfully joined the class"]);

        }
    }

    public function leave_class($id,Request $request){
        $userData               =    session()->get('user_data');
        $membersObj  =   new Class_members_model();

        $validateJoin =   $membersObj::select('*')->where('memberId',$userData['id'])->where('classId',$id)->get()
            ->toArray();

        if(count($validateJoin)>0){


            $membersObj::where('memberId',$userData['id'])->where('classId',$id)->delete();

            return redirect()->route('view_class',[$id,'success'=>"Successfully leaved the class"]);
        }else{
            return redirect()->route('view_class',[$id,'error'=>"You are not joined this class"]);

        }
    }
    public function leave_from_class($id,$userid,Request $request){
        $userData               =    session()->get('user_data');
        $classObj   =   new Class_model();

        $validate_class =   $classObj::select('*')->where("id",$id)->where("create_by",$userData['id'])->get()->toArray();

        if($validate_class>0){
            $membersObj  =   new Class_members_model();

            $validateJoin =   $membersObj::select('*')->where('memberId',$userid)->where('classId',$id)->get()
                ->toArray();

            if(count($validateJoin)>0){

                $membersObj::where('memberId',$userid)->where('classId',$id)->delete();

                return redirect()->route('view_class',[$id,'success'=>"Successfully removed from the class"]);
            }else{
                return redirect()->route('view_class',[$id,'error'=>"You are not joined this class"]);

            }
        }else{
            return redirect()->route('view_class',[$id,'error'=>"You are not admin of this class"]);

        }
    }
    public function add_admin($id,$userid,Request $request){
        $userData               =    session()->get('user_data');
        $classObj   =   new Class_model();

        $validate_class =   $classObj::select('*')->where("id",$id)->where("create_by",$userData['id'])->get()->toArray();

        if($validate_class>0){
            $membersObj  =   new Class_members_model();

            $validateJoin =   $membersObj::select('*')->where('memberId',$userid)->where('classId',$id)->get()
                ->toArray();

            if(count($validateJoin)>0){

                $classObj::where('id',$id)->update(array('adminId'=>$userid));

                return redirect()->route('view_class',[$id,'success'=>"Successfully added admin to the class"]);
            }else{
                return redirect()->route('view_class',[$id,'error'=>"Member not joined this class"]);

            }
        }else{
            return redirect()->route('view_class',[$id,'error'=>"You are not admin of this class"]);

        }
    }
    public function delete_class($id,Request $request){
        $userData               =    session()->get('user_data');
        $classObj   =   new Class_model();

        $validate_class =   $classObj::select('*')->where("id",$id)->where("create_by",$userData['id'])->get()->toArray();

        if($validate_class>0){
            $classObj::where('id',$id)->delete();

            return redirect()->route('user_home',['success'=>"Successfully deleted the class"]);
        }else{
            return redirect()->route('view_class',[$id,'error'=>"You are not admin of this class"]);

        }
    }
    public function submit_assignment($id,Request $request){
        $userData               =    session()->get('user_data');
        $membersObj             =   new Class_members_model();
        $classId                =   $request->input('classId');

        $validateJoin =   $membersObj::select('*')->where('memberId',$userData['id'])->where('classId',$classId)->get()
            ->toArray();

        if(count($validateJoin)>0){
            $assignmentObj = new Assignment_model();
            $assignmentData = $assignmentObj::select('*')->where('classId', $classId)->where('id', $id)
                ->get()->toArray();

            $checkPayments  =   \DB::select("call get_payments_user(".$classId.",".$userData['id'].")");


            if(count($checkPayments)>0 || ($assignmentData[0]['payment_required']<>'Payment Required' || $assignmentData[0]['payment_required']==null )) {


                $validate = $assignmentObj::select('*')->where('classId', $classId)->where('id', $id)->where('due_date',
                    '>=', date("Y-m-d"))
                    ->get()->toArray();

                if (count($validate) > 0) {
                    $assignmentPostObj = new Assignment_submit_model();

                    $validate = $assignmentPostObj::select("*")->where("postBy", $userData['id'])->where('assignId',
                        $id)
                        ->get()->toArray();
                    if (count($validate) > 0) {
                        return redirect()->route('view_class', [$classId, 'error' => "You have already submitted."]);

                    } else {
                        $result = $this->fileUpload3($request, 'assignment', $id);

                        if (!empty($result)) {
                            $data = array(
                                "postBy" => $userData['id'],
                                "assignId" => $id,
                                "file" => $result,
                                "submit_date" => date("Y-m-d H:i:s")
                            );

                            $assignmentPostObj::insert($data);
                            return redirect()->route('view_class',
                                [$classId, 'success' => "Successfully submitted assignment"]);

                        } else {
                            return redirect()->route('view_class', [$classId, 'error' => "Some error occured"]);

                        }
                }
            }else {
                    return redirect()->route('view_class',[$classId,'error'=>"This is not a valid assignment, Maybe due dated assignment"]);

            }
            }else{
                return redirect()->route('view_class',[$classId,'error'=>"Please sette this month payments to submit."]);
            }
        }else{
            return redirect()->route('view_class',[$classId,'error'=>"You are not joined this class"]);

        }
    }

    public function create_assignment($id,Request $request){
        $userData = session()->get('user_data');
        $rule =   $this->rules2();
        $data  =  $request->except(array('_token')) ;
        $validator = Validator::make($data,$rule);

        //if validation fails
        if($validator->fails()){
            $msg    =   json_decode($validator->messages());
            foreach ($msg as $row){

                return redirect()->route('view_class',[$id,'error'=>$row[0]]);
            }

        }else{
            $classObj  =   new Class_model();

            $validatePost =   $classObj::select('*')->where('create_by',$userData['id'])->where('id',$id)->get()
                ->toArray();

            if(count($validatePost)>0){
                $assignmentObj  =   new Assignment_model();

                $data   =   array(
                                    "assignmentName"=>$request->input("assignmentName"),
                                    "classId"=>$id,
                                    "postBy"=>$userData['id'],
                                    "description"=>$request->input("description"),
                                    "due_date"=>$request->input("due_date"),
                                    "payment_required"=>$request->input("payment_required"),
                                    "create_date"=>date("Y-m-d H:i:s"),
                                    );

                $assignmentObj::insert($data);
                return redirect()->route('view_class',[$id,'success'=>"Successfully created the assignment"]);

            }else{
                return redirect()->route('view_class',[$id,'error'=>"Denied to create the assignment"]);
            }
        }
    }
    public function update_assignment($id,Request $request){
        $userData = session()->get('user_data');
        $rule =   $this->rules2();
        $data  =  $request->except(array('_token')) ;
        $validator = Validator::make($data,$rule);

        //if validation fails
        if($validator->fails()){
            $msg    =   json_decode($validator->messages());
            foreach ($msg as $row){

                return redirect()->route('view_class',[$id,'error'=>$row[0]]);
            }

        }else{
            $assignmentObj  =   new Assignment_model();

            $validate =   $assignmentObj::select('*')->where('postBy',$userData['id'])->where('id',$id)->get()
                ->toArray();

            if(count($validate)>0){

                $data   =   array(
                                    "assignmentName"=>$request->input("assignmentName"),
                                    "postBy"=>$userData['id'],
                                    "description"=>$request->input("description"),
                                    "due_date"=>$request->input("due_date"),
                                    "payment_required"=>$request->input("payment_required"),
                                    "create_date"=>date("Y-m-d H:i:s"),
                                    );

                $assignmentObj::where('id',$id)->update($data);
                return redirect()->route('view_class',[$validate[0]['classId'],'success'=>"Successfully updated the assignment"]);

            }else{
                return redirect()->route('home',[$id,'error'=>"Denied to update the assignment"]);
            }
        }
    }
    public function delete_assignment($id,Request $request){
        $userData = session()->get('user_data');

        $assignmentObj  =   new Assignment_model();

            $validate =   $assignmentObj::select('*')->where('postBy',$userData['id'])->where('id',$id)->get()
                ->toArray();

            if(count($validate)>0){

                $assignmentObj::where('id',$id)->delete();
                return redirect()->route('view_class',[$validate[0]['classId'],'success'=>"Successfully deleted the assignment"]);

            }else{
                return redirect()->route('home',['error'=>"Denied to delete the assignment"]);
            }

    }

    public function set_grades($assignId,$classId,$grade,Request $request){
        $classObj   =   new Class_model();
        $userData       =    session()->get('user_data');

        $validate_class =   $classObj::select('*')->where("id",$classId)->where("create_by",$userData['id'])->get()->toArray();

        if(count($validate_class)>0){
            $assigmentSubmitModel   =   new Assignment_submit_model();
            $validate_assignment    =   $this->validate_assignment($classId,$assignId);

            if(count($validate_assignment)>0){

                $assigmentSubmitModel::where('id',$assignId)->update(array("grade"=>$grade));

                $mailObj    =   new Send_email();
                $mailObj->sendMail(array($validate_assignment[0]['emailAddress']),
                                    $validate_assignment[0]['assignmentName']." Grades Updated",'Dear '.$validate_assignment[0]['userName']
                                    .'<br>'.$validate_class[0]['className']." class assignment " .$validate_assignment[0]['assignmentName']
                                    . ' Grades Updated'
                                    . '<br>'.'Your Grade is : '.$grade

                );

                return redirect()->route('view_class',[$classId,'success'=>"Successfully updated the grades"]);
            }else{
                return redirect()->route('view_class',[$classId,'error'=>"This assignment not belong to this class"]);

            }

        }else{
            return redirect()->route('view_class',[$classId,'error'=>"This class not created by you"]);

        }
    }

    public function validate_assignment($classId,$assignId){
        $assigmentSubmitModel   =   new Assignment_submit_model();
        $result =   $assigmentSubmitModel::select(\DB::raw("tbl_assignments.assignmentName,tbl_assignments.classId,users.userName,users.emailAddress"))
                    ->leftJoin('users','tbl_assignments_submit.postBy','=','users.id')
                    ->join('tbl_assignments','tbl_assignments.id','=','tbl_assignments_submit.assignId')
                    ->where("tbl_assignments.classId",$classId)
                    ->where("tbl_assignments_submit.id",$assignId)
                    ->orderBy('tbl_assignments_submit.grade')
                    ->orderBy('tbl_assignments_submit.id')
                    ->get()->toArray();

        return $result;
    }

    public function mark_attendance($id,Request $request){
        $count  =   $request->input("count");
        $date   =   date("Y-m-d");

        $classObj   =   new Class_model();
        $userData       =    session()->get('user_data');

        $validate_class =   $classObj::select('*')->where("id",$id)->where("create_by",$userData['id'])->get()->toArray();

        if(count($validate_class)>0) {

            $attendanceObj = new Attendance_model();

            for ($i = 0; $i < $count; $i++) {
                $studentId = $request->input('user_id_' . $i);
                $userPresent = $request->input('user_present_' . $i);

                $attendanceObj::where("classId", $id)->where("studentId", $studentId)->delete();

                $data = array(
                    "studentId" => $studentId,
                    "classId" => $id,
                    "markBy" => $userData['id'],
                    "present" => (int)$userPresent,
                    "date" => $date
                );

                $attendanceObj::insert($data);

            }
            return redirect()->route('view_class',[$id,'success'=>"Successfully updated the attendance"]);

        }else{
            return redirect()->route('view_class',[$id,'error'=>"You are not the creator of this class"]);

        }
    }
    public function mark_payments($id,Request $request){
        $count  =   $request->input("count");
        $date   =   date("Y-m-d");

        $classObj   =   new Class_model();
        $userData       =    session()->get('user_data');

        $validate_class =   $classObj::select('*')->where("id",$id)->where("adminId",$userData['id'])->get()->toArray();

        if(count($validate_class)>0) {

            $payModal = new Payments_model();

            for ($i = 0; $i < $count; $i++) {
                $studentId = $request->input('user_id_' . $i);
                $userPay = $request->input('user_pay_' . $i);

                $payModal::where("classId", $id)->where("payBy", $studentId)->delete();

                $data = array(
                    "payBy" => $studentId,
                    "receiveBy" => $userData['id'],
                    "classId" => $id,
                    "paid" => (int)$userPay,
                    "payDate" => date("Y-m-d"),
                    "create_date" => date("Y-m-d"),
                    "payMonth" => date("Y-m"),
                );

                $payModal::insert($data);

            }
            return redirect()->route('view_class',[$id,'success'=>"Successfully added the payment"]);

        }else{
            return redirect()->route('view_class',[$id,'error'=>"You are not the admin of this class"]);

        }
    }
    public function rules2(){
        return [
            'assignmentName' => 'required|max:50|min:3',
            'description' => 'max:100',
            'due_date' => 'required',
        ];
    }
}
