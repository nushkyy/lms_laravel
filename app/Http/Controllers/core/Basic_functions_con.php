<?php

namespace App\Http\Controllers\core;

use App\Models\core\Class_model;
use App\Models\core\User_model;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class Basic_functions_con extends Controller
{
    //

    public function search_nav(Request $request){
        $search                 =   $request->input("search");

        //$data['userData']       =    session()->get('user_data');
        $userModel              =   new User_model();
        $data['usersList']    = $userModel::select(\DB::raw('id,userName,address,userType,contactNo,fullName,emailAddress
                                                    ,profilepic'))
            ->where('userName','like','%'.$search.'%')
            ->get()->toArray();
        $classModel              =   new Class_model();
        $data['classList']      = $classModel::select(\DB::raw('*'))
            ->where('className','like','%'.$search.'%')
            ->orWhere('classDescription','like','%'.$search.'%')
            ->get()->toArray();

        echo json_encode($data);

    }
}
