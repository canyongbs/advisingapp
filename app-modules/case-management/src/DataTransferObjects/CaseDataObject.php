<?php

namespace AdvisingApp\CaseManagement\DataTransferObjects;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

class CaseDataObject extends Data
{
    public function __construct(
        public string|Optional $division_id,
        public string|Optional $status_id,
        public string $type_id,
        public string|Optional $priority_id,
        public string|Optional $title,
        public string|Optional $close_details,
        public string|Optional $res_details,
        public string $respondent_id,
    ) {}

    public static function fromData(array $data): static
    {
        return new self(
            division_id: $data['division_id'] ?? Optional::create(),
            status_id: $data['status_id'] ?? Optional::create(),
            type_id: $data['type_id'],
            priority_id: $data['priority_id'] ?? Optional::create(),
            title: $data['title'] ?? Optional::create(),
            close_details: $data['close_details'] ?? Optional::create(),
            res_details: $data['res_details'] ?? Optional::create(),
            respondent_id: $data['respondent_id'],
        );
    }
}
