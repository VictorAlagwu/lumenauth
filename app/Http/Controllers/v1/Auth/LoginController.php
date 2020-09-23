<?php

namespace App\Http\Controllers\v1\Auth;

use App\Domain\Dto\Request\User\LoginDto;
use App\Domain\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function authenticate(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string',
            'password' => 'required|string',
        ]);
        if ($validator->fails()) {
            return ApiResponse::responseValidationError($validator);
        }
        $dto = new LoginDto($request->email, $request->password);
        
        $response = $this->userService->login($dto);
        if (!$response->status) {
            return ApiResponse::responseError([], $response->message);
        }
        $options = [
            'token' => $response->token,
            'token_type' => $response->token_type,
            'expires_in' => $response->expires_in
        ];
        return ApiResponse::responseSuccess($response->data ?? [], $response->message, $options);
    }
}
