<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Session;

class checkRegister extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */

    public function testBasicTest()
    {
        Session::start();
        $response=$this->post(route('save_user'), array(
            'username'=>'test1234',
            'password'=>'test1234567',
            'firstName'=>'Test',
            'lastName'=>'Test',
            'location'=>'Test Location',
            'emailAddress'=>'test@test.com',
            'contactNo'=>'1234567890',

            '_token' => csrf_token()))
            ;
        $response->assertStatus(200);
    }
}
