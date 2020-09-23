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

    //TODO: User should get an error when wrong company ID is provided
    //TODO: User should get an error if existing email is used

    // public function testUserCanLogin()
    // {
    //     $user = factory('App\MUser')->create();

    //     $response = $this->json('POST', url('api/v1/login'), [
    //         'email' => 'test@gmail.com',
    //         'password' => 'secret1234',
    //     ]);
    //     //Assert it was successful and a token was received
    //     $response->assertStatus(200);
    //     $this->assertArrayHasKey('data', $response->json());
    //     //Delete the user
    //     User::where('email', 'test@gmail.com')->delete();
    // }

    //TODO: Users should get errors when using wrong credentials

}
