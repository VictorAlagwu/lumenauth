<?php

namespace App\Domain\Dto\Value\User;

/**
 * UserServiceResposeDto - Data transfer object for user service
 */
class UserServiceResponseDto
{
    public bool $status;
    public string $message;
    public ?array $data;
    public ?string $token;
    public ?string $token_type;
    public ?int $expires_in;

    /**
     * @param boolean $status
     * @param string $message
     * @param array|null $data
     * @param string|null $token
     * @param string|null $token_type
     * @param int|null $expires_in
     */
    public function __construct(
        bool $status,
        string $message,
        ?array $data = [],
        ?string $token = null,
        ?string $token_type = null,
        ?int $expires_in = null
    ) {
        $this->status = $status;
        $this->message = $message;
        $this->data = $data;
        $this->token = $token;
        $this->token_type = $token_type;
        $this->expires_in = $expires_in;
    }
}
