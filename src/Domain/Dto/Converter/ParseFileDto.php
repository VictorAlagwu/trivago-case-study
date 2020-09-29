<?php

namespace App\Domain\Dto\Converter;

class ParseFileDto
{
    public bool $status;
    public ?object $data;
    public string $message;


    /**
     * @param boolean $status
     * @param string $message
     */
    public function __construct(
        bool $status,
        ?object $data,
        string $message
    ) {
        $this->status = $status;
        $this->data = $data;
        $this->message = $message;
    }
}
