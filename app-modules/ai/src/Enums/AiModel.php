<?php

/*
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

    Advising App™ is licensed under the Elastic License 2.0. For more details,
    see https://github.com/canyongbs/advisingapp/blob/main/LICENSE.

    Notice:

    - You may not provide the software to third parties as a hosted or managed
      service, where the service provides users with access to any substantial set of
      the features or functionality of the software.
    - You may not move, change, disable, or circumvent the license key functionality
      in the software, and you may not remove or obscure any functionality in the
      software that is protected by the license key.
    - You may not alter, remove, or obscure any licensing, copyright, or other notices
      of the licensor in the software. Any use of the licensor’s trademarks is subject
      to applicable law.
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Advising App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace AdvisingApp\Ai\Enums;

use Filament\Support\Contracts\HasLabel;
use AdvisingApp\Ai\Services\TestAiService;
use AdvisingApp\Ai\Services\Contracts\AiService;
use AdvisingApp\IntegrationOpenAi\Services\OpenAiGpt4Service;
use AdvisingApp\IntegrationOpenAi\Services\OpenAiGpt35Service;
use AdvisingApp\IntegrationOpenAi\Services\OpenAiGpt4oService;
use AdvisingApp\IntegrationOpenAi\Services\OpenAiGptTestService;

enum AiModel: string implements HasLabel
{
    case OpenAiGpt35 = 'openai_gpt_3.5';

    case OpenAiGpt4 = 'openai_gpt_4';

    case OpenAiGpt4o = 'openai_gpt_4o';

    case OpenAiGptTest = 'openai_gpt_test';

    case Test = 'test';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::OpenAiGpt35 => 'OpenAI GPT-3.5',
            self::OpenAiGpt4 => 'OpenAI GPT-4',
            self::OpenAiGpt4o => 'OpenAI GPT-4o',
            self::OpenAiGptTest => 'OpenAI GPT Test',
            self::Test => 'Test',
        };
    }

    public function getService(): AiService
    {
        $service = match ($this) {
            self::OpenAiGpt35 => OpenAiGpt35Service::class,
            self::OpenAiGpt4 => OpenAiGpt4Service::class,
            self::OpenAiGpt4o => OpenAiGpt4oService::class,
            self::OpenAiGptTest => OpenAiGptTestService::class,
            self::Test => TestAiService::class,
        };

        app()->scopedIf($service);

        return app($service);
    }

    public function isVisibleForApplication(AiApplication $aiApplication): bool
    {
        return match ($this) {
            self::OpenAiGpt35, self::OpenAiGpt4o => $aiApplication === AiApplication::PersonalAssistant,
            self::OpenAiGpt4 => $aiApplication === AiApplication::ReportAssistant,
            self::OpenAiGptTest => false,
            self::Test => true,
        };
    }

    public function isSharedDeployment(AiModel $model): bool
    {
        if ($this === $model) {
            return true;
        }

        $deployment = $model->getService()->getDeployment();

        if (blank($deployment)) {
            return false;
        }

        return $deployment === $this->getService()->getDeployment();
    }

    public static function parse(string | self $value): self
    {
        if ($value instanceof self) {
            return $value;
        }

        return self::from($value);
    }
}
