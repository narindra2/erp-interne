<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LoginTest extends TestCase
{

    public function test_login_form() {
        $response = $this->get("/login");
        $response->assertStatus(200);
    }
}
