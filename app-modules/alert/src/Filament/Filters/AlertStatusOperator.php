<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS LLC. All rights reserved.

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
      of the licensor in the software. Any use of the licensor's trademarks is subject
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

namespace AdvisingApp\Alert\Filament\Filters;

use AdvisingApp\Alert\Models\AlertConfiguration;
use Filament\Forms\Components\Select;
use Filament\QueryBuilder\Constraints\Operators\Operator;
use Filament\Schemas\Components\Component;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class AlertStatusOperator extends Operator
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->name('alertStatus');

        $this->label(fn (bool $isInverse): string => $isInverse
            ? 'does not have alert'
            : 'has alert');

        $this->summary(function (bool $isInverse, ?array $settings): string {
            if (blank($settings)) {
                return '';
            }

            $alertConfigurationId = $settings['alert_configuration_id'] ?? null;
            $status = $settings['status'] ?? null;

            $alertName = 'Unknown Alert';

            if ($alertConfigurationId) {
                $config = AlertConfiguration::find($alertConfigurationId);
                $alertName = $config?->preset->getLabel() ?? 'Unknown Alert';
            }

            $statusLabel = ($status === '1' || $status === true) ? 'True' : 'False';

            $prefix = $isInverse ? 'does not have' : 'has';

            return "{$prefix} alert \"{$alertName}\" = {$statusLabel}";
        });
    }

    /**
     * @return array<Component>
     */
    public function getFormSchema(): array
    {
        return [
            Select::make('alert_configuration_id')
                ->label('Alert')
                ->options(
                    fn (): array => AlertConfiguration::query()
                        ->where('is_enabled', true)
                        ->get()
                        ->sortBy(fn (AlertConfiguration $config) => $config->preset->getDisplayOrder())
                        ->mapWithKeys(fn (AlertConfiguration $config) => [
                            $config->getKey() => $config->preset->getLabel(),
                        ])
                        ->all()
                )
                ->required()
                ->searchable(),
            Select::make('status')
                ->label('Alert Status')
                ->options([
                    '1' => 'True',
                    '0' => 'False',
                ])
                ->required()
                ->default('1'),
        ];
    }

    /**
     * @param  Builder<Model>  $query
     *
     * @return Builder<Model>
     */
    public function applyToBaseQuery(Builder $query): Builder
    {
        $settings = $this->getSettings();
        $alertConfigurationId = $settings['alert_configuration_id'] ?? null;
        $status = $settings['status'] ?? null;

        if (blank($alertConfigurationId)) {
            return $query;
        }

        $wantsTrue = $status === '1' || $status === true;
        $shouldHave = $wantsTrue xor (bool) $this->isInverse();

        $method = $shouldHave ? 'whereHas' : 'whereDoesntHave';

        return $query->{$method}(
            'studentAlerts',
            fn (Builder $query) => $query->where('alert_configuration_id', $alertConfigurationId)
        );
    }
}
