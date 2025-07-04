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
        public string|Optional $close_details,
        public string|Optional $res_details,
        public string $respondent_type,
        public string $respondent_id,
    ) {}

    /**
     * @param array<string, string|Optional|null> $data
     *
     * @return self
     */
    public static function fromData(array $data): self
    {
        return new self(
            division_id: $data['division_id'] ?? Optional::create(),
            status_id: $data['status_id'] ?? Optional::create(),
            type_id: $data['type_id'],
            priority_id: $data['priority_id'] ?? Optional::create(),
            close_details: $data['close_details'] ?? Optional::create(),
            res_details: $data['res_details'] ?? Optional::create(),
            respondent_type: $data['respondent_type'],
            respondent_id: $data['respondent_id'],
        );
    }
}
