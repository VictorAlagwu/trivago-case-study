<?php

namespace App\Domain\Dto\Converter;

class ConverterRequestDto
{
    public string $name;
    public ?string $sortBy;
    public ?string $filterBy;
    public ?string $filterValue;
    public ?string $groupBy;


    /**
     * @param string $name
     * @param string|null $sortBy
     * @param string|null $filterBy
     * @param string|null $filterValue
     * @param string|null $groupBy
     */
    public function __construct(
        string $name,
        ?string $sortBy,
        ?string $filterBy,
        ?string $filterValue,
        ?string $groupBy
    ) {
        $this->name = $name;
        $this->sortBy = $sortBy;
        $this->filterBy = $filterBy;
        $this->filterValue = $filterValue;
        $this->groupBy = $groupBy;
    }
}
