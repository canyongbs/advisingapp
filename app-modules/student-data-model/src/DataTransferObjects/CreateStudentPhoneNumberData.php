<?php

namespace AdvisingApp\StudentDataModel\DataTransferObjects;

use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Optional;

#[MapName(SnakeCaseMapper::class)]
class CreateStudentPhoneNumberData extends Data
{
    public function __construct(
        public string $number,
        public string|Optional|null $type,
        public int|Optional|null $order,
        public int|Optional|null $ext,
        public bool $canReceiveSms = false,
    ) {}
}
