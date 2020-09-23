<?php

namespace App\Domain\Dto\Request\User;

class LoginDto
{
    public $email;
    public $password;

    public function __construct(
        string $email,
        string $password
    ) {
        $this->email = $email;
        $this->password = $password;
    }
}
