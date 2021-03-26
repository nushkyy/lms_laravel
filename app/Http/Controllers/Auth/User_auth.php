<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Response;
use App\Http\Controllers\Controller;
use App\Models\Auth\User_auth_model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;

class User_auth extends Controller
{
    //

    public function login(Request $request){
        $data['msg']    =   $request->input("success");
        return view("auth.login",$data);
    }
    public function register(){
        return view("auth.register");
    }
    public function save_user(Request $request){
        $rule =   $this->rules();
        $data  =  $request->except(array('_token')) ;
        $validator = Validator::make($data,$rule);

        //if validation fails
        if($validator->fails()){
            $msg    =   json_decode($validator->messages());
            $errors =   array();
            $i      =   0;

            foreach ($msg as $row){
                $errors[$i]   =   $row;
                $i++;
            }

            $result =   array("code"=>2,"result"=>$msg);
            echo json_encode($result);
        }else{

            $save_array   =   array(
                "userName"  =>$request->input("username"),
                "password"  =>sha1($request->input("password")),
                "fullName"     =>$request->input("fullName"),
                "contactNo"       =>$request->input("contactNo"),
                "emailAddress"  =>$request->input("emailAddress"),
                "address"        =>$request->input("address"),
                "userType"        =>$request->input("userType"),
                "reg_ip"        =>$request->ip(),
            );
            $userObj  =   new User_auth_model();

            $userObj::insert($save_array);

            $result =   array("code"=>1,"result"=>"Sucessfully saved");
            echo json_encode($result);
        }
    }
    public function rules($id=null){
        //validation rules
        $userValidate   =   "unique:users,username";

        if($id!=null){
            $userValidate.= ",".$id;
        }
        return [
            'username' => 'required|'.$userValidate.'|max:30|min:5',
            'password' => 'required|max:30|min:5',
            'fullName' => 'required|max:30|min:1',
            'emailAddress' => 'required|email|max:50|min:5',
            'contactNo' => 'required|regex:/[0-9]{10}/',

           // 'currency' => 'required|max:20|not_in:0',
        ];
    }

    public function check_login(Request $request){
        $username   =   $request->input("username");
        $password   =   $request->input("password");

        $loginObj   =   new User_auth_model();

        $verifyUser =   $loginObj::where('username',$username)->where("password",sha1($password))->get()->toArray();


        if(count($verifyUser)>0){

            $res        =   array("code"=>"1","auth_code"=>sha1(uniqid()),"result"=>"Successfully Logged In");
            $response   =   Response::make($res, 200);

            $response->header('Content-Type', "application/json");

            $user_data  =   [
                "username"=>$verifyUser[0]["userName"],
                "fullname"=>$verifyUser[0]["fullName"],
                "userType"=>$verifyUser[0]["userType"],
                "id"=>$verifyUser[0]["id"],
                "contactNo"=>$verifyUser[0]["contactNo"],
                "parentId"=>$verifyUser[0]["parentId"],
            ];

            session()->put('user_data', $user_data);

            return $response;
        }else{

            $res        =   array("code"=>"2","result"=>"Username and password not match");
            $response   =   Response::make($res, 406);

            $response->header('Content-Type', "application/json");

            return $response;
        }
    }

    public function logout(Request $request){
        $request->session()->forget('user_data');

        redirect()->route('home')->send();
    }

}
