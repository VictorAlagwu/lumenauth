<?php

namespace App\Services;

use App\Domain\Dto\Request\User\CreateDto;
use App\Domain\Dto\Request\User\LoginDto;
use App\Domain\Dto\Value\User\UserServiceResponseDto;
use App\Models\User;
use App\Repositories\User\IUserRepository;
use Illuminate\Support\Facades\Auth;

class UserService
{
    protected $userRepository;

    public function __construct(IUserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function login(LoginDto $dto): UserServiceResponseDto
    {
        $token = Auth::attempt(['email' => $dto->email, 'password' => $dto->password]);

        if (!$token) {
            return new UserServiceResponseDto(false, 'Invalid credentials');
        }
        $user = $this->userRepository->find(Auth::user()->id);

        return new UserServiceResponseDto(
            true,
            'Login successful',
            $user->toArray(),
            $token,
            'Bearer',
            Auth::factory()->getTTL() * 60
        );
    }

    public function register(CreateDto $dto): UserServiceResponseDto
    {
        $user = $this->userRepository->create(
            [
                'name' => $dto->name,
                'email' => $dto->email,
                'password' => app('hash')->make($dto->password)
            ]
        );

        return new UserServiceResponseDto(true, 'New user registered', $user->toArray());
    }

    public function getUser(string $userId): ?User
    {
        return $this->userRepository->findOrFail($userId);
    }
}
