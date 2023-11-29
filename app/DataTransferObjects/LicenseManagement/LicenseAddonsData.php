<?php

namespace App\DataTransferObjects\LicenseManagement;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapInputName(SnakeCaseMapper::class)]
class LicenseAddonsData extends Data
{
    public function __construct(
        public bool $onlineAdmissions,
        public bool $realtimeChat,
        public bool $dynamicForms,
        public bool $conductSurveys,
        public bool $personalAssistant,
        public bool $serviceManagement,
        public bool $knowledgeManagement,
        public bool $studentAndProspectPortal,
    ) {}
}
