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

namespace AdvisingApp\Campaign\Filament\Blocks;

use Carbon\CarbonImmutable;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\DateTimePicker;
use AdvisingApp\Campaign\Settings\CampaignSettings;
use AdvisingApp\Engagement\Enums\EngagementDeliveryMethod;
use AdvisingApp\Campaign\Filament\Blocks\Actions\DraftCampaignEngagementBlockWithAi;
use AdvisingApp\Engagement\Filament\Resources\EngagementResource\Fields\EngagementSmsBodyField;

class EngagementBatchSmsBlock extends CampaignActionBlock
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Text Message');

        $this->schema($this->createFields());
    }

    public function generateFields(string $fieldPrefix = ''): array
    {
        return [
            Hidden::make($fieldPrefix . 'delivery_method')
                ->default(EngagementDeliveryMethod::Sms->value),
            EngagementSmsBodyField::make(context: 'create', fieldPrefix: $fieldPrefix),
            Actions::make([
                DraftCampaignEngagementBlockWithAi::make()
                    ->deliveryMethod(EngagementDeliveryMethod::Sms)
                    ->mergeTags([
                        'student first name',
                        'student last name',
                        'student full name',
                        'student email',
                        'student preferred name',
                    ]),
            ]),
            DateTimePicker::make('execute_at')
                ->label('When should the journey step be executed?')
                ->columnSpanFull()
                ->timezone(app(CampaignSettings::class)->getActionExecutionTimezone())
                ->helperText(app(CampaignSettings::class)->getActionExecutionTimezoneLabel())
                ->lazy()
                ->hint(fn ($state): ?string => filled($state) ? $this->generateUserTimezoneHint(CarbonImmutable::parse($state)) : null)
                ->required()
                ->minDate(now()),
        ];
    }

    public static function type(): string
    {
        return 'bulk_engagement_sms';
    }
}
