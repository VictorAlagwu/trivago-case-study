<?php

namespace App\Domain\Dto\Value;

class ConverterResponseDto
{
    public $status;
    public $message;

    /**
     * @param boolean $status
     * @param string $message
     */
    public function __construct(
        bool $status,
        string $message
    ) {
        $this->status = $status;
        $this->message = $message;
    }
}
