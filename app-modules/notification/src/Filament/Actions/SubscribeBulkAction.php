<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

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

namespace AdvisingApp\Notification\Filament\Actions;

use Exception;
use App\Models\User;
use Illuminate\Support\Str;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Actions\BulkAction;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use AdvisingApp\Prospect\Models\Prospect;
use Filament\Tables\Columns\TextInputColumn;
use Illuminate\Database\Eloquent\Collection;
use AdvisingApp\StudentDataModel\Models\Student;
use AdvisingApp\Notification\Actions\SubscriptionToggle;
use AdvisingApp\Notification\Models\Contracts\Subscribable;

class SubscribeBulkAction
{
    public static function make(string $context): BulkAction
    {
        return BulkAction::make('bulkSubscription')
              ->icon('heroicon-s-bell')
              ->modalHeading('Create Bulk Subscription')
              ->modalDescription(
                fn (Collection $records) => "You have selected {$records->count()} " . Str::plural($context, $records->count()) . ' to subscribe.'
              )
              ->form([
                  Select::make('user_ids')
                        ->label('Who should be subscribed?')
                        ->options(User::all()->pluck('name', 'id'))
                        ->multiple()
                        ->searchable()
                        ->default([auth()->id()])
                        ->required()
                        ->exists('users', 'id'),
                  Toggle::make('remove_prior')
                      ->label('Remove all prior subscriptions?')
                      ->default(false)
                      ->hintIconTooltip('If checked, all prior subscriptions will be removed.'),
              ])
              ->action(function(array $data, Collection $records) use($context) {
                    $records->each(function ($record) use ($data,$context) {

                      throw_unless($record instanceof Student || $record instanceof Prospect, new Exception("Record must be of type {$context}."));

                      $removePrior = $data['remove_prior'];
                      $userIds = $data['user_ids'] ?? [];

                      if ($removePrior) {
                          $record->subscriptions()->delete();
                      }

                      foreach ($userIds as $userId) {
                          $record->subscriptions()
                              ->firstOrCreate([
                                  'subscribable_id' => $record->getKey(),
                                  'subscribable_type' => $record->getMorphClass(),
                                  'user_id' => $userId,
                              ]);
                      }
                      
                    });

                  Notification::make()
                        ->title('Subscriptions created successfully.')
                        ->success()
                        ->send();
                })
                ->deselectRecordsAfterCompletion();
    }
}
