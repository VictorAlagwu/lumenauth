<?php

/** @var \Laravel\Lumen\Routing\Router $router */

use App\Domain\Helpers\ApiResponse;

$router->get('/', function () use ($router) {
    $options = [
        'Device Info' => request()->header('User-Agent') ?? '',
        'Your IP' => request()->ip() ?? ''
    ];

    return ApiResponse::responseSuccess([], 'Welcome to LumenAuth', $options);
});

$router->group(['prefix' => 'api/v1'], function () use ($router) {
    //Authentication
    $router->post('login', ['uses' => 'v1\Auth\LoginController@authenticate']);
    $router->post('register', ['uses' => 'v1\Auth\RegisterController@store']);

    //Auth Route
    $router->group(['middleware' => 'auth'], function () use ($router) {
        $router->get('user', ['uses' => 'v1\UserController@index']);
    });
});
