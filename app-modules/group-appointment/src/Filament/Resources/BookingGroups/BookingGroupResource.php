<?php

namespace AdvisingApp\GroupAppointment\Filament\Resources\BookingGroups;

use AdvisingApp\GroupAppointment\Filament\Resources\BookingGroups\Pages\CreateBookingGroup;
use AdvisingApp\GroupAppointment\Filament\Resources\BookingGroups\Pages\EditBookingGroup;
use AdvisingApp\GroupAppointment\Filament\Resources\BookingGroups\Pages\ListBookingGroups;
use AdvisingApp\GroupAppointment\Filament\Resources\BookingGroups\Pages\ViewBookingGroup;
use AdvisingApp\GroupAppointment\Filament\Resources\BookingGroups\Schemas\BookingGroupForm;
use AdvisingApp\GroupAppointment\Filament\Resources\BookingGroups\Schemas\BookingGroupInfolist;
use AdvisingApp\GroupAppointment\Filament\Resources\BookingGroups\Tables\BookingGroupsTable;
use AdvisingApp\GroupAppointment\Models\BookingGroup;
use App\Filament\Clusters\GroupAppointment;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class BookingGroupResource extends Resource
{
    protected static ?string $model = BookingGroup::class;

    protected static ?string $cluster = GroupAppointment::class;

    public static function form(Schema $schema): Schema
    {
        return BookingGroupForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return BookingGroupInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BookingGroupsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBookingGroups::route('/'),
            'create' => CreateBookingGroup::route('/create'),
            'view' => ViewBookingGroup::route('/{record}'),
            'edit' => EditBookingGroup::route('/{record}/edit'),
        ];
    }
}
