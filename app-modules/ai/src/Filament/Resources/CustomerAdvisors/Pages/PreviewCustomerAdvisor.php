<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS Inc. All rights reserved.

    Advising App® is licensed under the Elastic License 2.0. For more details,
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
    - Canyon GBS Inc. respects the intellectual property rights of others and expects the
      same in return. Canyon GBS® and Advising App® are registered trademarks of
      Canyon GBS Inc., and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS Inc.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace AdvisingApp\Ai\Filament\Resources\CustomerAdvisors\Pages;

use AdvisingApp\Ai\Filament\Resources\CustomerAdvisors\CustomerAdvisorResource;
use AdvisingApp\Ai\Models\CustomerAdvisor;
use App\Features\LlamaParseLinkParsingFeature;
use App\Models\User;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Computed;
use UnitEnum;

class PreviewCustomerAdvisor extends ViewRecord
{
    protected static string $resource = CustomerAdvisorResource::class;

    protected static string | UnitEnum | null $navigationGroup = 'Configuration';

    protected static ?string $navigationLabel = 'Preview';

    protected static ?string $title = 'Preview';

    protected static ?string $breadcrumb = 'Preview';

    protected string $view = 'ai::filament.resources.customer-advisors.pages.preview-customer-advisor';

    public static function canAccess(array $parameters = []): bool
    {
        /** @var User $user */
        $user = auth()->user();

        return $user->can('viewAny', CustomerAdvisor::class) && ($user->can('create', CustomerAdvisor::class) || $user->can('update', $parameters['record'])) && parent::canAccess($parameters);
    }

    #[Computed]
    public function isFailed(): bool
    {
        $advisor = $this->getRecord();

        assert($advisor instanceof CustomerAdvisor);

        if (! LlamaParseLinkParsingFeature::active()) {
            // TODO: Cleanup Task (llamaparse-link-parsing): delete this whole `if` block, as the file check is repeated below.
            return $advisor->files()->whereNull('parsing_results')->where('updated_at', '<', now()->subHour())->exists()
                || $advisor->links()->whereNull('parsing_results')->where('updated_at', '<', now()->subHour())->exists();
        }

        // A link that still has content keeps the advisor usable, even when refreshing it failed.
        return $advisor->files()->whereNull('parsing_results')->where('updated_at', '<', now()->subHour())->exists()
            || $advisor->links()
                ->whereNull('parsing_results')
                ->whereNull('parsing_job_id')
                ->where(fn (Builder $query): Builder => $query
                    ->whereNotNull('parsing_failed_at')
                    ->orWhere('created_at', '<', now()->subHour()))
                ->exists();
    }

    #[Computed]
    public function isProcessing(): bool
    {
        $advisor = $this->getRecord();

        assert($advisor instanceof CustomerAdvisor);

        if (! LlamaParseLinkParsingFeature::active()) {
            // TODO: Cleanup Task (llamaparse-link-parsing): delete this whole `if` block, as the file check is repeated below.
            return $advisor->files()->whereNull('parsing_results')->where('updated_at', '>=', now()->subHour())->exists()
                || $advisor->links()->whereNull('parsing_results')->where('updated_at', '>=', now()->subHour())->exists();
        }

        // A link without content is still being learned while its parse job runs, or while it waits to be submitted.
        return $advisor->files()->whereNull('parsing_results')->where('updated_at', '>=', now()->subHour())->exists()
            || $advisor->links()
                ->whereNull('parsing_results')
                ->where(fn (Builder $query): Builder => $query
                    ->whereNotNull('parsing_job_id')
                    ->orWhere(fn (Builder $query): Builder => $query
                        ->whereNull('parsing_failed_at')
                        ->where('created_at', '>=', now()->subHour())))
                ->exists();
    }
}
