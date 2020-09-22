<?php

namespace Tests\Unit\Services;

use App\Services\UserService;
use Tests\TestCase;

/**
 * Class UserServiceTest.
 *
 * @covers \App\Services\UserService
 */
class UserServiceTest extends TestCase
{
    /**
     * @var UserService
     */
    protected $userService;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->userService = new UserService();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->userService);
    }

    public function testLogin(): void
    {
        /** @todo This test is incomplete. */
        $this->markTestIncomplete();
    }

    public function testRegister(): void
    {
        /** @todo This test is incomplete. */
        $this->markTestIncomplete();
    }

    public function testGetUser(): void
    {
        /** @todo This test is incomplete. */
        $this->markTestIncomplete();
    }
}
