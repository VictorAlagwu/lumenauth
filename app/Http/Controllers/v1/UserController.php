<?php

namespace App\Http\Controllers\v1;

use App\Domain\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    protected $userService;

    public function __construct(
        UserService $userService
    ) {
        $this->userService = $userService;
    }

    public function index()
    {
        $user = $this->userService->getUser(request()->user()->id);
        return ApiResponse::responseSuccess($user->toArray() ?? [], 'User\'s details');
    }
}
