<?php

namespace App\Domain\Dto\Converter;

class ConverterResponseDto
{
    public bool $status;
    public string $message;
    public ?string $path;

    /**
     * @param boolean $status
     * @param string $message
     * @param string|null $path
     */
    public function __construct(
        bool $status,
        string $message,
        ?string $path
    ) {
        $this->status = $status;
        $this->message = $message;
        $this->path = $path;
    }
}
