<?php

namespace Tests\Feature;

use Tests\TestCase;

class LoginTest extends TestCase
{
    public function test_login()
    {
        $response = $this->post('/login', [
            'username' => 'admin',
            'password' => 'UntukIndonesiaRaya123'
        ]);

        $response->dump();
    }
}
