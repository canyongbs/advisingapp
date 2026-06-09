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

namespace AdvisingApp\Ai\Filament\Resources\CustomerAdvisors;

use AdvisingApp\Ai\Filament\Resources\CustomerAdvisors\Pages\CreateCustomerAdvisor;
use AdvisingApp\Ai\Filament\Resources\CustomerAdvisors\Pages\CustomerAdvisorEmbed;
use AdvisingApp\Ai\Filament\Resources\CustomerAdvisors\Pages\EditCustomerAdvisor;
use AdvisingApp\Ai\Filament\Resources\CustomerAdvisors\Pages\EditCustomerAdvisorLinks;
use AdvisingApp\Ai\Filament\Resources\CustomerAdvisors\Pages\ListCustomerAdvisors;
use AdvisingApp\Ai\Filament\Resources\CustomerAdvisors\Pages\ManageCategories;
use AdvisingApp\Ai\Filament\Resources\CustomerAdvisors\Pages\ManageCustomerAdditionalKnowledge;
use AdvisingApp\Ai\Filament\Resources\CustomerAdvisors\Pages\ManageCustomerAdvisorResourceHub;
use AdvisingApp\Ai\Filament\Resources\CustomerAdvisors\Pages\ManageCustomerQuestions;
use AdvisingApp\Ai\Filament\Resources\CustomerAdvisors\Pages\PreviewCustomerAdvisor;
use AdvisingApp\Ai\Filament\Resources\CustomerAdvisors\Pages\ViewCustomerAdvisor;
use AdvisingApp\Ai\Models\CustomerAdvisor;
use App\Features\RenameQnaAdvisorsFeature;
use Filament\Pages\Page;
use Filament\Resources\Resource;
use Override;
use UnitEnum;

class CustomerAdvisorResource extends Resource
{
    protected static ?string $model = CustomerAdvisor::class;

    protected static string | UnitEnum | null $navigationGroup = 'Chatbots';

    protected static ?string $modelLabel = 'Customer Advisor';

    protected static ?int $navigationSort = 10;

    protected static ?string $slug = 'customer-advisors';

    #[Override]
    public static function canAccess(): bool
    {
        return RenameQnaAdvisorsFeature::active() ? (auth()->user()->can('customer_advisor.view-any') && parent::canAccess()) : parent::canAccess();
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCustomerAdvisors::route('/'),
            'create' => CreateCustomerAdvisor::route('/create'),
            'view' => ViewCustomerAdvisor::route('/{record}'),
            'edit' => EditCustomerAdvisor::route('/{record}/edit'),
            'edit-websites' => EditCustomerAdvisorLinks::route('/{record}/websites'),
            'manage-categories' => ManageCategories::route('/{record}/categories'),
            'manage-questions' => ManageCustomerQuestions::route('/{record}/questions'),
            'manage-additional-knowledge' => ManageCustomerAdditionalKnowledge::route('/{record}/additional-knowledge'),
            'manage-resource-hub' => ManageCustomerAdvisorResourceHub::route('/{record}/resource-hub'),
            'preview' => PreviewCustomerAdvisor::route('/{record}/preview'),
            'embed' => CustomerAdvisorEmbed::route('/{record}/embed'),
        ];
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            ViewCustomerAdvisor::class,
            EditCustomerAdvisor::class,
            ManageCategories::class,
            ManageCustomerQuestions::class,
            ManageCustomerAdditionalKnowledge::class,
            ManageCustomerAdvisorResourceHub::class,
            EditCustomerAdvisorLinks::class,
            PreviewCustomerAdvisor::class,
            CustomerAdvisorEmbed::class,
        ]);
    }
}
