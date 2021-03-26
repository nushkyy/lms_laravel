<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Session;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class googleApiTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testBasicTest()
    {
        Session::start();
        $response=$this->get(route('nearby_places'), array(
            'lonlat'=>'',
            '_token' => csrf_token()))
        ;
        $response->assertStatus(200);
    }
}
