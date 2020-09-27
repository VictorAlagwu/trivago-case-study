<?php

namespace App\Domain\Dto\Request\Converter;

class ParseFileDto
{
    public bool $status;
    public $data;
    public string $message;


    /**
     * @param boolean $status
     * @param string $message
     */
    public function __construct(
        bool $status,
        $data,
        string $message
    ) {
        $this->status = $status;
        $this->data = $data;
        $this->message = $message;
    }
}
