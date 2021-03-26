<?php

namespace App\Http\Controllers\core;

use App\Models\core\Location_model;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class Location_con extends Controller
{
    //
    public function save_location(Request $request){
        $locationObj    =   new Location_model();
        $userData       =   session()->get('user_data');

        $data           =   array(
                                    "location"=>$request->input("add"),
                                    "longitude"=>$request->input("lat"),
                                    "latitude"=>$request->input("long"),
                                    "userId"=>$userData['id'],
                                    "time"=>date("Y-m-d H:i:s"),
        );

        $locationObj::insert($data);
    }

    public function view_map(){
        $locationObj    =   new Location_model();
        $userData       =   session()->get('user_data');

        $data['locations']  =   $locationObj::select("*")->where("userId",$userData['id'])->get()->toArray();
        $data['i']                  =   0;
        return view("main.map_view",$data);
    }
}
