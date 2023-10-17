<?php

namespace Assist\Engagement\Models;

use App\Models\User;
use App\Models\BaseModel;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\TextInput;
use Assist\Campaign\Models\CampaignAction;
use Assist\Engagement\Actions\CreateEngagementBatch;
use Assist\Engagement\Enums\EngagementDeliveryMethod;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Assist\Engagement\Models\Concerns\HasManyEngagements;
use Assist\Campaign\Models\Contracts\ExecutableFromACampaignAction;
use Assist\Engagement\DataTransferObjects\EngagementBatchCreationData;

/**
 * @mixin IdeHelperEngagementBatch
 */
class EngagementBatch extends BaseModel implements ExecutableFromACampaignAction
{
    use HasManyEngagements;

    protected $fillable = [
        'user_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function executeFromCampaignAction(CampaignAction $action): void
    {
        ray('executeFromCampaignAction()', $action->campaign->caseload->retrieveRecords());

        CreateEngagementBatch::dispatch(EngagementBatchCreationData::from([
            'user' => $action->campaign->user_id,
            'records' => $action->campaign->caseload->retrieveRecords(),
            'subject' => $action->data['subject'],
            'body' => $action->data['body'],
            'deliveryMethods' => $action->data['delivery_methods'],
        ]));

        // Do we need to be able to relate campaigns/actions to the RESULT of their actions?
    }

    public static function getEditFormFields(): array
    {
        return [
            Fieldset::make('Bulk Engagement Details')
                ->schema([
                    TextInput::make('data.subject')
                        ->required()
                        ->maxLength(255),
                    TextInput::make('data.body')
                        ->required()
                        ->maxLength(255),
                    Select::make('data.delivery_methods')
                        ->label('How would you like to send this engagement?')
                        ->translateLabel()
                        ->options(EngagementDeliveryMethod::class)
                        ->multiple()
                        ->minItems(1)
                        ->validationAttribute('Delivery Methods')
                        ->helperText('You can select multiple delivery methods.')
                        ->reactive(),
                ]),
        ];
    }
}
