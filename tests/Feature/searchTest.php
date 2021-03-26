<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Session;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class searchTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testBasicTest()
    {
        Session::start();
        $response=$this->post(route('search_hotel'), array(
            'date'=>date("Y-m-d"),
            'location'=>'Test',
            '_token' => csrf_token()))
        ;
        $response->assertStatus(200);
    }
}
