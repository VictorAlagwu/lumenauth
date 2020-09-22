<?php

namespace App\Http\Controllers\v1\Auth;

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
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string',
                'email' => 'required|email|unique:users',
                'password' => 'required',
            ]);

            if ($validator->fails()) {
                return ApiResponse::responseValidationError($validator);
            }

            $user = $this->userService->register($request->convertToDto());
            return ApiResponse::responseCreated($user->data, $user->message);
        } catch (\Exception $e) {
            return ApiResponse::responseException($e, 400, 'Unable to register user');
        }
    }
}