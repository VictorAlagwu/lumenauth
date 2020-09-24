<?php

namespace Tests\Feature\Auth;

use Database\Factories\UserFactory;
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
        $data = [
            'id' => Str::uuid(),
            'name' => Str::random(6),
            'email' => 'demo@gmail.com',
            'password' => Hash::make('password')
        ];

        $this->post('api/v1/register', $data)
            ->seeStatusCode(201);
    }
    public function testNewUserShouldNotBeAbleToUseDuplicateEmail()
    {
        $data = [
            'id' => Str::uuid(),
            'name' => Str::random(6),
            'email' => 'demo@gmail.com',
            'password' => Hash::make('password')
        ];

        $this->post('api/v1/register', $data)
            ->seeStatusCode(201);

        $this->post('api/v1/register', $data)
            ->seeStatusCode(422);
    }

    public function testUserCanLogin()
    {
        $this->markTestIncomplete();
        $user = UserFactory::new()->create();

        $this->actingAs($user)->post('api/v1/login', [
            'email' => $user->email,
            'password' => $user->password
        ]);
        $this->seeStatusCode(200);
    }

    public function testUserCannotLoginWithInvalidCredentials()
    {
        $this->post('api/v1/login', [
            'email' => 'email@dmd.d',
            'password' => 'sdsd'
        ]);
        $this->seeStatusCode(400);
    }

    public function testUserLoginWithOutRequiredData()
    {
        $this->post('api/v1/login', [
            'email' => 'ewe'
        ]);
        $this->seeStatusCode(422);
    }
}
