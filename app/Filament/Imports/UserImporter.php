<?php

namespace App\Filament\Imports;

use App\Models\User;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Models\Import;
use App\Notifications\SetPasswordNotification;

class UserImporter extends Importer
{
    protected static ?string $model = User::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name')
                ->rules(['required'])
                ->requiredMapping()
                ->example('Jonathan Smith'),
            ImportColumn::make('email')
                ->rules(['required', 'email'])
                ->requiredMapping()
                ->example('johnsmith@gmail.com'),
            ImportColumn::make('job_title')
                ->rules(['required'])
                ->requiredMapping()
                ->example('12345'),
            ImportColumn::make('emplid')
                ->rules(['string'])
                ->example('12345'),
            ImportColumn::make('is_external')
                ->label('External User')
                ->boolean()
                ->rules(['boolean'])
                ->example('yes'),
        ];
    }

    public function resolveRecord(): ?User
    {
        $user = User::where('email', $this->data['email'])->first();

        return $user ?? new User([
            'email' => $this->data['email'],
        ]);
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your user import has completed and ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }

    protected function afterCreate(): void
    {
        ray('UserImporter.afterCreate()');

        /** @var User $user */
        $user = $this->getRecord();

        if ($user->is_external) {
            return;
        }

        $user->notify(new SetPasswordNotification());
    }
}
