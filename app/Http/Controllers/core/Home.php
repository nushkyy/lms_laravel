<?php

namespace App\Http\Controllers\core;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class Home extends Controller
{
    //
    public function index(){
        $data   =   array();

        return view("home.main",$data);
    }



}
