<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class AuthTest extends TestCase
{
    protected $client;
    protected $token;
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
}
