<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use App\Models\Api\Location_model;

class Location_con extends Controller
{
    //
    public function search_location(){
        $search =   Input::get("search");

        $locationObj    =   new Location_model();
        $result =   $locationObj::select(\DB::raw("cities.id,cities.country_id,cities.name,countries.name AS 
                                    'countryName'"))->join("countries","countries.id","cities.country_id")
                                    ->where("cities.name","like","%".$search."%")
                                    ->orWhere(\DB::raw("CONCAT(cities.name,' ',countries.name)"),"like","%".$search."%")
                                    ->limit(10)->get();


        echo json_encode($result->toArray());
    }
}
