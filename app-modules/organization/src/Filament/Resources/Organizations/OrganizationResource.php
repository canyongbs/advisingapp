<?php

namespace AdvisingApp\Organization\Filament\Resources\Organizations;

use AdvisingApp\Organization\Filament\Resources\Organizations\Pages\ListOrganizations;
use AdvisingApp\Organization\Filament\Resources\Organizations\Pages\ViewOrganization;
use AdvisingApp\Organization\Filament\Resources\Organizations\Schemas\OrganizationForm;
use AdvisingApp\Organization\Filament\Resources\Organizations\Schemas\OrganizationInfolist;
use AdvisingApp\Organization\Filament\Resources\Organizations\Tables\OrganizationsTable;
use AdvisingApp\Organization\Models\Organization;
use App\Enums\NavigationGroup;
use App\Features\OrganizationFeature;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

class OrganizationResource extends Resource
{
    protected static ?string $model = Organization::class;

    protected static string | UnitEnum | null $navigationGroup = NavigationGroup::Crm;

    protected static ?int $navigationSort = 140;

    protected static ?string $recordTitleAttribute = 'name';

    public static function canAccess(): bool
    {
        return OrganizationFeature::active() && parent::canAccess();
    }

    public static function form(Schema $schema): Schema
    {
        return OrganizationForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return OrganizationInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return OrganizationsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListOrganizations::route('/'),
            'view' => ViewOrganization::route('/{record}'),
        ];
    }
}
