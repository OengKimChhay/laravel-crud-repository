<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_the_application_returns_a_successful_response()
    {
        $response = $this->post('/api/auth/login',[
            'email' => 'chihe333@gmail.com',
            'password' =>'12345'
        ]);
        return $response;
        $response->assertStatus(200);
    }
}
