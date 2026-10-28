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

namespace AdvisingApp\Engagement\Filament\Actions;

use AdvisingApp\Engagement\Models\EmailTemplate;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Expression;
use Illuminate\Support\Str;

class LoadEmailTemplateAction
{
    public static function make(): Action
    {
        return Action::make('loadEmailTemplate')
            ->schema([
                Select::make('emailTemplate')
                    ->searchable()
                    ->options(fn (Get $get): array => self::getEmailTemplatesQuery($get)
                        ->orderBy('name')
                        ->limit(50)
                        ->pluck('name', 'id')
                        ->toArray())
                    ->getSearchResultsUsing(fn (Get $get, string $search): array => self::getEmailTemplatesQuery($get)
                        ->where(new Expression('lower(name)'), 'like', '%' . Str::lower($search) . '%')
                        ->orderBy('name')
                        ->limit(50)
                        ->pluck('name', 'id')
                        ->toArray())
                    ->getOptionLabelUsing(fn (Get $get, mixed $value): ?string => blank($value)
                        ? null
                        : self::getEmailTemplatesQuery($get)->whereKey($value)->value('name')),
                Checkbox::make('onlyMyTemplates')
                    ->label('Only show my templates')
                    ->live()
                    ->afterStateUpdated(fn (Set $set): mixed => $set('emailTemplate', null)),
                Checkbox::make('onlyMyDepartmentTemplates')
                    ->label("Only show my department's templates")
                    ->live()
                    ->afterStateUpdated(fn (Set $set): mixed => $set('emailTemplate', null)),
            ])
            ->action(function (array $data, RichEditor $component): void {
                $template = EmailTemplate::find($data['emailTemplate']);

                if (! $template) {
                    return;
                }

                $supportedMergeTags = array_keys($component->getMergeTags());

                $component->state(
                    (is_array($template->content) && filled($supportedMergeTags))
                        ? self::removeUnsupportedMergeTags($template->content, $supportedMergeTags)
                        : $template->content,
                );
            });
    }

    /**
     * Email templates can use merge tags that the editor cannot fill, which would be sent blank, so they are
     * removed when the template is loaded and the user can review the content before saving. Editors that do not
     * declare any merge tags keep the template content unchanged.
     *
     * @param array<string, mixed> $node
     * @param array<string> $supportedMergeTags
     *
     * @return array<string, mixed>
     */
    private static function removeUnsupportedMergeTags(array $node, array $supportedMergeTags): array
    {
        if (! is_array($node['content'] ?? null)) {
            return $node;
        }

        $node['content'] = collect($node['content'])
            ->reject(fn (mixed $child): bool => is_array($child)
                && in_array($child['type'] ?? null, ['mergeTag', 'rawHtmlMergeTag'], true)
                && (! in_array($child['attrs']['id'] ?? null, $supportedMergeTags, true)))
            ->map(fn (mixed $child): mixed => is_array($child) ? self::removeUnsupportedMergeTags($child, $supportedMergeTags) : $child)
            ->values()
            ->all();

        return $node;
    }

    /**
     * A user without a department has no department templates, so that filter then matches none.
     *
     * @return Builder<EmailTemplate>
     */
    private static function getEmailTemplatesQuery(Get $get): Builder
    {
        $user = auth()->user();
        assert($user instanceof User);

        return EmailTemplate::query()
            ->when(
                $get('onlyMyTemplates'),
                fn (Builder $query) => $query->whereBelongsTo($user)
            )
            ->when(
                $get('onlyMyDepartmentTemplates'),
                fn (Builder $query) => $query->whereIn('user_id', $user->department?->users->pluck('id') ?? [])
            );
    }
}
