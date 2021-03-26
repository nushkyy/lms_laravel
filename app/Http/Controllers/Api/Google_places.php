<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class Google_places extends Controller
{
    //
    public function get_nearby_places(Request $request){

        $places =   array();
        $z      =   0;

            $lonlat =   $request->input("lonlat");
            $type =   $request->input("type");
            $url    =   "https://maps.googleapis.com/maps/api/place/nearbysearch/json?location=".$lonlat
                        ."&radius=1500&type=".$type."&language=ro&key=AIzaSyD7VjWzVqSOsCIib_hUQ-mv-ry5wzVWTAg";
            $curl_handle=curl_init();
            curl_setopt($curl_handle,CURLOPT_URL,$url);
            curl_setopt($curl_handle,CURLOPT_CONNECTTIMEOUT,2);
            curl_setopt($curl_handle,CURLOPT_RETURNTRANSFER,1);
            $buffer = curl_exec($curl_handle);
            curl_close($curl_handle);
            $buffer =   json_decode($buffer);
            foreach($buffer->results as $row){
                if($z>10){
                    break;
                }
                if(isset($row)){
                    $res    =   $row;

                    $name       =   "";
                    $address    =   "";
                    $photo      =   "";
                    $rating     =   "";

                    if(isset($res->name)){
                        $name=$res->name;
                    }

                    if(isset($res->vicinity)){
                        $address=$res->vicinity;
                    }

                    if(isset($res->photos[0]->photo_reference)){
                        $photo=$res->photos[0]->photo_reference;
                    }
                    if(isset($res->rating)){
                        $rating=$res->rating;
                    }


                    $places[$z] =   array(
                                            "name"=>$name,
                                            "address"=>$address,
                                            "photo"=>$photo,
                                            "rating"=>$rating,
                    );
                    $z++;
                }
            }


        echo json_encode($places);
    }
}
