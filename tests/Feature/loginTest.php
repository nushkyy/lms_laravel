<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Session;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class loginTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testBasicTest()
    {
        Session::start();
        $response=$this->post(route('signin'), array(
            'username'=>'test',
            'password'=>'test123',
            '_token' => csrf_token()))
        ;
        $response->assertStatus(200);
    }
}
