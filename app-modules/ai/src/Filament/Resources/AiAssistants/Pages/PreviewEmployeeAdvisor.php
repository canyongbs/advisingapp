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

namespace AdvisingApp\Ai\Filament\Resources\AiAssistants\Pages;

use AdvisingApp\Ai\Filament\Resources\AiAssistants\AiAssistantResource;
use AdvisingApp\Ai\Models\AiAssistant;
use AdvisingApp\Ai\Models\AiThread;
use App\Models\User;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Str;
use Livewire\Attributes\Locked;
use UnitEnum;

class PreviewEmployeeAdvisor extends ViewRecord
{
    protected static string $resource = AiAssistantResource::class;

    protected static string | UnitEnum | null $navigationGroup = 'Configuration';

    protected static ?string $navigationLabel = 'Preview';

    protected static ?string $title = 'Preview';

    protected static ?string $breadcrumb = 'Preview';

    protected string $view = 'ai::filament.resources.ai-assistants.pages.preview-employee-advisor';

    #[Locked]
    public ?AiThread $thread = null;

    public static function canAccess(array $parameters = []): bool
    {
        /** @var User $user */
        $user = auth()->user();

        return $user->can('viewAny', AiAssistant::class)
            && ($user->can('create', AiAssistant::class) || $user->can('update', $parameters['record']))
            && parent::canAccess($parameters);
    }

    public function mount(int | string $record): void
    {
        parent::mount($record);

        /** @var AiAssistant $assistant */
        $assistant = $this->getRecord();

        $thread = new AiThread();
        $thread->assistant()->associate($assistant);
        $thread->user()->associate(auth()->user());
        $thread->is_preview = true;
        $thread->save();

        $this->thread = $thread;
    }

    /**
     * @return array<int|string, string|null>
     */
    public function getBreadcrumbs(): array
    {
        $resource = static::getResource();

        /** @var AiAssistant $record */
        $record = $this->getRecord();

        /** @var array<string, string> $breadcrumbs */
        $breadcrumbs = [
            $resource::getUrl() => $resource::getBreadcrumb(),
            $resource::getUrl('edit', ['record' => $record]) => Str::limit($record->name, 16),
            ...(filled($breadcrumb = $this->getBreadcrumb()) ? [$breadcrumb] : []),
        ];

        if (filled($cluster = static::getCluster())) {
            return $cluster::unshiftClusterBreadcrumbs($breadcrumbs);
        }

        return $breadcrumbs;
    }
}
