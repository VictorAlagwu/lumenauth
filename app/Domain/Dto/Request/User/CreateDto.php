<?php

namespace App\Domain\Dto\Request\User;

class CreateDto
{
    public $name;
    public $email;
    public $password;

    public function __construct(
        string $name,
        string $email,
        string $password
    ) {
        $this->name = $name;
        $this->email = $email;
        $this->password = $password;
    }
}
