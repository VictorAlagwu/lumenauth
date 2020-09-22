<?php

namespace App\Services;

use App\Domain\Dto\Value\User\UserServiceResponseDto;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserService
{
    //A DTO should be used as request param rather than Request
    public function login(Request $request): UserServiceResponseDto
    {
        $credentials = $request->only(['email', 'password']);

        if (!$token = Auth::attempt($credentials)) {
            return new UserServiceResponseDto(false, 'Invalid credentials');
        }
        $user = User::find(Auth::user()->id);

        return new UserServiceResponseDto(
            true,
            'Login successful',
            $user->toArray(),
            $token,
            'Bearer',
            Auth::factory()->getTTL() * 60
        );
    }

    public function register(Request $request): UserServiceResponseDto
    {
        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = app('hash')->make($request->password);

        $user->save();

        return new UserServiceResponseDto(true, 'New user registered');
    }

    public function getUser(string $userId): ?User
    {
        return User::where('id', $userId)->first();
    }
}
