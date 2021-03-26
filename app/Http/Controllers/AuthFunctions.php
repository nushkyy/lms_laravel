<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class AuthFunctions extends Controller
{
    //Get logged in user informations
    public function get_user_data(){

        return session()->get('user_data');
    }

    //get any form validation error msg
    public function get_validation_errors(){
        $validation_errors  =   session()->get('validation_errors');
        session()->put('validation_errors','');
        return json_decode($validation_errors);
    }
}
