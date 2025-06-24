<?php

namespace AdvisingApp\Ai\Filament\Resources\QnaAdvisorResource\Pages;

use AdvisingApp\Ai\Filament\Resources\QnaAdvisorResource;
use App\Features\QnaAdvisorFeature;
use App\Models\User;
use Filament\Resources\Pages\Page;

class QnaAdvisorEmbed extends Page
{
    protected static string $resource = QnaAdvisorResource::class;

    protected static string $view = 'filament.pages.coming-soon';

    protected static ?string $navigationGroup = 'Configuration';

    protected static ?string $title = 'Embed';

    public static function canAccess(array $parameters = []): bool
    {
        /** @var User $user */
        $user = auth()->user();

        return QnaAdvisorFeature::active() && $user->can('qna_advisor_embed.view-any') && $user->can('qna_advisor_embed.*.view') && parent::canAccess($parameters);
    }
}
