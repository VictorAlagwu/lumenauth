<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use DatabaseMigrations;

    public function setUp(): void
    {
        parent::setUp();
    }

    public function testNewUserShouldBeAbleToRegister()
    {
        $this->markTestIncomplete();
        $data = [
            'id' => Str::uuid(),
            'name' => Str::random(6),
            'email' => 'demo@gmail.com',
            'password' => Hash::make('password')
        ];

        $this->post('api/v1/register', $data)
            ->seeStatusCode(201);
    }

    public function testUserCanLogin()
    {
        $this->markTestIncomplete();
    }
}
