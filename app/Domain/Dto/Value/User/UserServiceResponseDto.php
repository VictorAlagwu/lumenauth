<?php

namespace App\Domain\Dto\Value\User;

/**
 * UserServiceResposeDto - Data transfer object for user service
 */
class UserServiceResponseDto
{
    public $status;
    public $message;
    public $data;
    public $token;
    public $token_type;
    public $expires_in;

    /**
     * @param boolean $status
     * @param string $message
     * @param array|null $data
     * @param boolean|null $token
     * @param string|null $token_type
     * @param int|null $expires_in
     */
    public function __construct(
        bool $status,
        string $message,
        ?array $data = [],
        ?bool $token = null,
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
