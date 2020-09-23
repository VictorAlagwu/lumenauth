<?php

namespace Tests\Feature;

use App\Models\User;
use Carbon\Carbon;
use Database\Factories\UserFactory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class UserTest extends TestCase
{
    use DatabaseMigrations;

    public function setUp(): void
    {
        parent::setUp();
    }

    public function testUserCanViewProfile()
    {
        $user = UserFactory::new()->create();

        $this->actingAs($user)->get('api/v1/user')
            ->seeStatusCode(200);
    }

    public function testUserIsMakingUnauthorizedRequest()
    {
        $this->get('api/v1/user')->seeStatusCode(401);
        // $this->assertEquals(401, $response->status());
    }
}
