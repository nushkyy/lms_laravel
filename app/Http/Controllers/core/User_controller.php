<?php

namespace App\Http\Controllers\core;

use App\Http\Controllers\AuthCon;
use App\Models\core\Assignment_model;
use App\Models\core\Class_members_model;
use App\Models\core\Class_model;
use App\Models\core\Location_model;
use App\Models\core\Message_model;
use Illuminate\Http\Request;
use App\Models\core\User_model;
use App\Models\core\Follow_model;
use App\Models\Auth\User_auth_model;
use Illuminate\Support\Facades\Validator;

class User_controller extends AuthCon
{
    //
    public function index(){
        $data['userData']   =    session()->get('user_data');
        $userData           =    session()->get('user_data');

        $userModel              =   new User_model();
        $data['profileData']    = $userModel::select(\DB::raw('*'))
                                            ->where('userName',$data['userData']['username'])
                                            ->get()->toArray();

        $msgModel              =   new Message_model();
        $data['messages']     = $msgModel::select(\DB::raw('tbl_messages.*,users.profilepic,users.userName'))
                                            ->where('receiver',$data['userData']['id'])
                                            ->leftJoin('users','tbl_messages.sender','=','users.id')
                                            ->where('seen','0')
                                            ->get()->toArray();

        $data['notifications']     = \DB::table('notifications_view')->select('*')
                                            ->where('userid',$data['userData']['id'])
                                            ->orderBy('date','DESC')
                                            ->get()->toArray();

        $data['randomUsers']    =   $this->get_random_users();

        $locationObj    =   new Location_model();

        $data['locations']  =   $locationObj::select("*")
                                ->where("userId",$data['userData']['id'])
                                ->where(\DB::raw("DATE(time)"),date("Y-m-d"))
                                ->orderBy('time','DESC')
                                ->get()->toArray();
        $classObj               =   new Class_model();
        $data['classData']      =   $classObj::select(\DB::raw("tbl_class.*,users.userName,tbl_class_members.id AS 'joinid'"))
            ->leftJoin('users','tbl_class.create_by','=','users.id')
            ->join('tbl_class_members',function ($join) use($userData){
                $userid =   (string)$userData['id'];
                $join->on('tbl_class_members.classId','=','tbl_class.id');
                $join->on('tbl_class_members.memberId','=',\DB::raw($userid));
            })
            ->orderBy("tbl_class.className")->get()->toArray();

        $assignmentsObj         =   new Assignment_model();

        $data['assignments']   =   $assignmentsObj::select(\DB::raw("tbl_assignments.*,tbl_assignments_submit.id AS 'submit',tbl_assignments_submit.submit_date,tbl_assignments_submit.grade"))
            ->leftJoin('tbl_assignments_submit',function ($join) use($userData){
                $userid =   (string)$userData['id'];
                $join->on('tbl_assignments_submit.assignId','=','tbl_assignments.id');
                $join->on('tbl_assignments_submit.postBy','=',\DB::raw($userid));
            })
            ->join('tbl_class_members',function ($join) use($userData){
                $userid =   (string)$userData['id'];
                $join->on('tbl_class_members.classId','=','tbl_assignments.classId');
                $join->on('tbl_class_members.memberId','=',\DB::raw($userid));
            })
            ->where("tbl_assignments_submit.id",null)->orderBy('tbl_assignments.due_date')
            ->get()->toArray();
        return view("home.main_view",$data);

    }

    public function updateProfilePic(Request $request){
        $userData   =    session()->get('user_data');

        $result = $this->fileUpload($request,'profilepic',$userData['username']);

        if(!empty($result)){

            $data                   =   array("profilepic"=>$result);

            $userModel              =   new User_model();
            $data['profileData']    =   $userModel::where("id",$userData['id'])->update($data);

            return redirect()->route('user_home');
        }
    }

    public function fileUpload( Request $request,$filename,$file) {

        $this->validate($request, [
            $filename => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($request->hasFile($filename)) {

            $image = $request->file($filename);
            $name = sha1($file).'.'.$image->getClientOriginalExtension();
            $destinationPath = public_path('/uploads/profilepic');
            $image->move($destinationPath, $name);
            return 'uploads/profilepic/'.$name;
           // return back()->with('success','Image Upload successfully');
        }
    }

    public function view_user($username){
        $data['userData']       =    session()->get('user_data');
        $userData               =    session()->get('user_data');
        $userModel              =   new User_model();
        $data['profileData']    = $userModel::select(\DB::raw('users.id,users.userName,users.address,users.userType,
                                                                users.contactNo,users.fullName,users.emailAddress,
                                                                users.parentId,users.about,
                                                                get_time_diff(TIMESTAMPDIFF(SECOND, 
                                                                COALESCE(users.dob,NOW()), NOW())) as "age"
                                                                ,
                                                                get_time_diff(TIMESTAMPDIFF(SECOND, 
                                                                users.last_activity, NOW())) as "last_activity"
                                                                ,tbl_follow.id AS "follow_id",profilepic'))
                                                ->where('users.id',$username)
                                                ->leftJoin('tbl_follow',function ($join) use($userData){
                                                    $userid =   (string)$userData['id'];
                                                    $join->on('tbl_follow.follow','=','users.id');
                                                    $join->on('tbl_follow.followby','=',\DB::raw($userid));
                                                })
                                                ->get()->toArray();
        $memberObj              =   new Class_members_model();
        $data['joinedClasses']  =   $memberObj::select(\DB::raw("tbl_class.className,tbl_class.id,tbl_class.profilepic"))
                                            ->leftJoin('tbl_class','tbl_class.id','=','tbl_class_members.classId')
                                            ->where("tbl_class_members.memberId",$username)
                                            ->get()->toArray();

        $followObj              =   new Follow_model();
        $data['followings']     =   $followObj::select(\DB::raw("users.userName,users.id,users.profilepic"))
                                            ->leftJoin('users','users.id','=','tbl_follow.follow')
                                            ->where("tbl_follow.followBy",$username)
                                            ->get()->toArray();

        $data['followedby']     =   $followObj::select(\DB::raw("users.userName,users.id,users.profilepic"))
                                            ->leftJoin('users','users.id','=','tbl_follow.followBy')
                                            ->where("tbl_follow.follow",$username)
                                            ->get()->toArray();
        $data['postLocations']  =   \DB::table('latest_locations')->select("*")->where('userId',$username)
                                                    ->limit(5)
                                                    ->get()->toArray();

        if($data['profileData'][0]['parentId']==$userData['id']){

            $assignmentsObj         =   new Assignment_model();

            $data['assignments']   =   $assignmentsObj::select(\DB::raw("tbl_assignments.*,tbl_class.className,tbl_assignments_submit.id AS 'submit',tbl_assignments_submit.submit_date,tbl_assignments_submit.grade"))
                ->leftJoin('tbl_assignments_submit',function ($join) use($username){
                    $join->on('tbl_assignments_submit.assignId','=','tbl_assignments.id');
                    $join->on('tbl_assignments_submit.postBy','=',\DB::raw($username));
                })
                ->leftJoin('tbl_class','tbl_class.id','=','tbl_assignments.classId')

                ->orWhereNotNull("tbl_assignments_submit.grade","","IS NOT NULL")
                ->orderBy('tbl_assignments.due_date')
                ->get()->toArray();
        }

        $locationObj    =   new Location_model();

        $data['locations']  =   $locationObj::select("*")
                                ->where("userId",$data['userData']['id'])
                                ->where(\DB::raw("DATE(time)"),date("Y-m-d"))
                                ->orderBy('time','DESC')
                                ->get()->toArray();

        return view("main.users.user_view",$data);

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

    public function set_parent_id($id)
    {
        $userData = session()->get('user_data');
        $userObj = new User_model();

        $validateP = $userObj::select('*')->where('id', $userData['id'])->get()
            ->toArray();

        if ((!empty($validateP[0]['parentId'])) && $validateP[0]['parentId']<>null) {
            return redirect()->route('view_user',[$id,'error'=>'Already you have assigned parent ID']);
        } else {

            $data = array(
                "parentId" => $id,
            );
            $userObj::where('id',$userData['id'])->update($data);
            $userData['parentId']   =   $id;
            return redirect()->route('view_user',[$id,'success'=>'Successfully Added Parent Control']);
        }
    }
    public function follow_user($id)
    {
        $userData = session()->get('user_data');
        $followObj = new Follow_model();

        $validateFollow = $followObj::select('*')->where('followBy', $userData['id'])->where('follow', $id)->get()
            ->toArray();

        if (count($validateFollow) > 0) {
            return redirect()->route('view_user',[$id,'error'=>'Already Following']);
        } else {

            $data = array(
                "followBy" => $userData['id'],
                "follow" => $id,
                "follow_date" => date("Y-m-d H:i:s")
            );
            $followObj::insert($data);

            return redirect()->route('view_user',[$id,'success'=>'Successfully Following']);
        }
    }

    public function change_password(Request $request){
        $userData   =   session()->get('user_data');
        $id         =   $userData['id'];
        $rule       =   $this->rules_pprofile();
        $data       =   $request->except(array('_token')) ;
        $validator  =   Validator::make($data,$rule);

        //if validation fails
        if($validator->fails()){
            $msg    =   json_decode($validator->messages());
            $msgshow    =   "";
            foreach($msg as $row){
                $msgshow    =   $row[0];
            }
            return redirect()->route('user_home',['error'=>$msgshow]);
        }else{
            $userObj  =   new User_auth_model();

            $password1  =   $request->input("password1");
            $password2  =   $request->input("password2");

            $validatePass   =   $userObj::select("*")->where('id',$userData['id'])->where('password',sha1($password1))
                                        ->get()->toArray();

            if(count($validatePass)>0){
                $update_array   =   array(
                    "password"     =>sha1($password2),
                );


                $userObj::where("id",$id)->update($update_array);

                return redirect()->route('user_home',['success'=>'Successfully updated password']);
            }else{
                return redirect()->route('user_home',['error'=>'Previous password not match']);
            }


        }
    }
    public function unfollow_user($id){
        $userData               =    session()->get('user_data');
        $followObj  =   new Follow_model();

        $validateFollow =   $followObj::select('*')->where('followBy',$userData['id'])->where('follow',$id)->get()
                                        ->toArray();

        if(count($validateFollow)>0){


            $followObj::where('followBy',$userData['id'])->where('follow',$id)->delete();

            return redirect()->route('view_user',[$id,'success'=>'Successfully Un-Followed']);
        }else{

            return redirect()->route('view_user',[$id,'error'=>'You Are Not Following']);
        }
    }

    public function update_profile(Request $request){
        $userData   =   session()->get('user_data');
        $id         =   $userData['id'];
        $rule       =   $this->rules_uprofile();
        $data       =   $request->except(array('_token')) ;
        $validator  =   Validator::make($data,$rule);

        //if validation fails
        if($validator->fails()){
            $msg    =   json_decode($validator->messages());
            $msgshow    =   "";
            foreach($msg as $row){
                $msgshow    =   $row[0];
            }
            return redirect()->route('user_home',['error'=>$msgshow]);
        }else{

            $save_array   =   array(
                "fullName"     =>$request->input("fullName"),
                "contactNo"       =>$request->input("contactNo"),
                "emailAddress"  =>$request->input("emailAddress"),
                "address"        =>$request->input("address"),
            );
            $userObj  =   new User_auth_model();


            $user_data  =   [
                "username"=>$userData["username"],
                "fullname"=>$request->input("fullName"),
                "userType"=>$userData["userType"],
                "id"=>$userData["id"],
                "contactNo"=>$request->input("contactNo"),
                "parentId"=>$userData["parentId"],
            ];

            session()->put('user_data', $user_data);

            $userObj::where("id",$id)->update($save_array);

            return redirect()->route('user_home',['success'=>'Successfully updated profile']);

        }
    }
    public function send_message(Request $request){
        $userData   =   session()->get('user_data');
        $id         =   $userData['id'];
        $rule       =   $this->rules_message();
        $data       =   $request->except(array('_token')) ;
        $validator  =   Validator::make($data,$rule);

        //if validation fails
        if($validator->fails()){
            $msg    =   json_decode($validator->messages());
            $msgshow    =   "";
            foreach($msg as $row){
                $msgshow    =   $row[0];
            }
            echo json_encode(array("code"=>3,"result"=>$msgshow));
        }else{

            $save_array   =   array(
                "sender"     =>$id,
                "receiver"       =>$request->input("msgto"),
                "message"  =>$request->input("message"),
                "sent_date"        =>date("Y-m-d H:i:s"),
            );
            $msgObj  =   new Message_model();
            $msgObj::insert($save_array);

            echo json_encode(array("code"=>1,"result"=>"Successfully sent message"));

        }
    }
    public function update_seen(){
        $userData   =   session()->get('user_data');
        $id         =   $userData['id'];

        $update_array   =   array(
            "seen"     =>1,
        );
        $msgObj  =   new Message_model();
        $msgObj::where('receiver',$id)->update($update_array);

    }


    public function rules_uprofile($id=null){
        return [
            'fullName' => 'required|max:30|min:1',
            'emailAddress' => 'required|email|max:50|min:5',
            'contactNo' => 'required|regex:/[0-9]{10}/',
        ];
    }
    public function rules_pprofile($id=null){
        return [
            'password1' => 'required',
            'password2' => 'required|max:30|min:5',
        ];
    }
    public function rules_message($id=null){
        return [
            'message' => 'required|max:30|min:1',
            'msgto' => 'required',
        ];
    }
}
