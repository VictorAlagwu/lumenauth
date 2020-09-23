<?php

namespace Tests\Unit\Services;

use App\Domain\Dto\Request\User\CreateDto;
use App\Domain\Dto\Request\User\LoginDto;
use App\Domain\Dto\Value\User\UserServiceResponseDto;
use App\Models\User;
use App\Repositories\User\IUserRepository;
use App\Services\UserService;
use Database\Factories\UserFactory;
use Illuminate\Support\Facades\Auth;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Mockery;
use Mockery\Mock;
use Tests\TestCase;

/**
 * Class UserServiceTest.
 *
 * @covers \App\Services\UserService
 */
class UserServiceTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * @var UserService
     */
    protected $userService;

    /**
     * @var IUserRepository|Mock
     */
    protected $userRepository;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->userRepository = Mockery::mock(IUserRepository::class);
        $this->userService = new UserService($this->userRepository);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->userService);
        unset($this->userRepository);
    }

    public function testLogin(): void
    {
        $res = new UserServiceResponseDto(
            false,
            'ss',
            [],
            'sdsd',
            'jj',
            1
        );
        $res->expires_in = Auth::factory()->getTTL() * 60;

        $user = UserFactory::new()->create();
        $dto = new LoginDto($user->email, $user->password);

        
        Auth::shouldReceive('attempt')->once()->andReturn('sdsd92233sds');
        Auth::shouldReceive('user')->once()->andReturn($user);

        $this->userRepository->shouldReceive('find')->once()->andReturn($user);


        $response = $this->userService->login($dto);
        $this->assertInstanceOf(UserServiceResponseDto::class, $response);
    }

    public function testLoginIsInvalid(): void
    {
        $user = UserFactory::new()->create();
        $dto = new LoginDto($user->email, $user->password);
        Auth::shouldReceive('attempt')->once()->andReturn(false);
        // Auth::shouldReceive('factory')->once()->andReturn();
        // Auth::shouldReceive('getTTL')->once()->andReturn();
        new UserServiceResponseDto(false, 'sd');

        $response = $this->userService->login($dto);
        $this->assertInstanceOf(UserServiceResponseDto::class, $response);
    }

    public function testRegister(): void
    {
        $dto = new CreateDto('test', 'email@mail.com', 'ds');
        $dto->password = app('hash')->make($dto->password);

        $this->userRepository->shouldReceive('create')->once()->andReturn(new User());

        $response = $this->userService->register($dto);
        $this->assertInstanceOf(UserServiceResponseDto::class, $response);
    }

    public function testGetUser(): void
    {
        $this->userRepository->shouldReceive('findOrFail')->once()->andReturn(new User());

        $response = $this->userService->getUser('wewewe-wewewe');

        $this->assertInstanceOf(User::class, $response);
    }
}
