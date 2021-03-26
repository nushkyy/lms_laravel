<?php
namespace App\Http\Controllers;

use App\Http\Controllers\AuthFunctions;
use App\Models\core\User_model;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;

class AuthCon extends Controller
{
    //
    public $user_data;
    public $error_msg;
    public $success_msg;
    public $validation_errors;

    public function __construct(Request $request)
    {
        // calling authenticate function controller
        $auth_functions     =   new AuthFunctions();

        //get logged in user details
        $req                =   $auth_functions->get_user_data();
        //get validation errors
        $validation_errors  =   $auth_functions->get_validation_errors();

        //assign user data to public variable
        $this->user_data    =   $req;


        $this->success_msg  =   $request->input("result");
        if(empty($this->success_msg)){
            $this->success_msg  =   $request->input("success");
        }
        if(empty($this->error_msg)){
            $this->error_msg  =   $request->input("error");
        }



        $this->validation_errors    =   array();
        if(!empty($validation_errors)){
            // assign validation errors to public variable
            $this->validation_errors    =   $validation_errors;
        }

        if(!isset($req['username'])){
            //if login validation error redirect to home pageQ
            redirect()->route('signin')->send();
        }else{
            $userObj    =   new User_model();
            $data       =   array("last_activity"=>date("Y-m-d H:i:s"));

            $userObj::where('id',$req['id'])->update($data);
        }
    }
}
