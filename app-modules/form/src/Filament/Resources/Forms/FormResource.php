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

namespace AdvisingApp\Form\Filament\Resources\Forms;

use AdvisingApp\Form\Filament\Resources\Forms\Pages\CreateForm;
use AdvisingApp\Form\Filament\Resources\Forms\Pages\EditForm;
use AdvisingApp\Form\Filament\Resources\Forms\Pages\ListForms;
use AdvisingApp\Form\Filament\Resources\Forms\Pages\ManageFormEmailAutoReply;
use AdvisingApp\Form\Filament\Resources\Forms\Pages\ManageFormSubmissions;
use AdvisingApp\Form\Filament\Resources\Forms\Pages\ManageFormWorkflows;
use AdvisingApp\Form\Filament\Resources\Forms\Pages\SubmissionOnScreenResponse;
use AdvisingApp\Form\Filament\Resources\Forms\Pages\ViewForm;
use AdvisingApp\Form\Models\Form;
use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class FormResource extends Resource
{
    protected static ?string $model = Form::class;

    protected static string | UnitEnum | null $navigationGroup = 'CRM';

    protected static ?int $navigationSort = 120;

    protected static ?string $navigationLabel = 'Online Forms';

    protected static ?string $breadcrumb = 'Online Forms';

    protected static ?string $modelLabel = 'Form';

    protected static ?string $recordTitleAttribute = 'name';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['fields']);
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            ViewForm::class,
            EditForm::class,
            SubmissionOnScreenResponse::class,
            ManageFormWorkflows::class,
            ManageFormSubmissions::class,
            ManageFormEmailAutoReply::class,
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListForms::route('/'),
            'create' => CreateForm::route('/create'),
            'edit' => EditForm::route('/{record}/edit'),
            'view' => ViewForm::route('/{record}'),
            'manage-on-screen-response' => SubmissionOnScreenResponse::route('/{record}/on-screen-response'),
            'manage-form-workflows' => ManageFormWorkflows::route('/{record}/workflows'),
            'manage-submissions' => ManageFormSubmissions::route('/{record}/submissions'),
            'manage-email-auto-reply' => ManageFormEmailAutoReply::route('/{record}/email-auto-reply'),
        ];
    }
}
