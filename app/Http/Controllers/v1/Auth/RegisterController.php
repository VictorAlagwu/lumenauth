<?php

namespace App\Http\Controllers\v1\Auth;

use App\Domain\Dto\Request\User\CreateDto;
use App\Domain\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return ApiResponse::responseValidationError($validator);
        }
        $dto = new CreateDto($request->name, $request->email, $request->password);
        $user = $this->userService->register($dto);
        return ApiResponse::responseCreated($user->data ?? [], $user->message);
    }
}
