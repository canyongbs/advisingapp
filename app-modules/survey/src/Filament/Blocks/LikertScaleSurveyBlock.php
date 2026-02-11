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

namespace AdvisingApp\Survey\Filament\Blocks;

use AdvisingApp\Form\Filament\Blocks\FormFieldBlock;
use AdvisingApp\Form\Models\Submissible;
use AdvisingApp\Form\Models\SubmissibleField;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\StudentDataModel\Models\Student;

class LikertScaleSurveyBlock extends FormFieldBlock
{
    public ?string $label = 'Likert Scale';

    public string $preview = 'survey::blocks.previews.likert';

    public string $rendered = 'survey::blocks.submissions.likert';

    public ?string $icon = 'heroicon-m-list-bullet';

    public static function type(): string
    {
        return 'likert';
    }

    /**
     * @return array<string, string|array<string, string>>
     */
    public static function getFormKitSchema(SubmissibleField $field, ?Submissible $submissible = null, Student|Prospect|null $author = null): array
    {
        return [
            '$formkit' => 'radio',
            'label' => $field->label,
            'name' => $field->getKey(),
            ...($field->is_required ? ['validation' => 'required'] : []),
            'options' => static::options(),
        ];
    }

    /**
     * @return array<string>
     */
    public static function getValidationRules(SubmissibleField $field): array
    {
        return [
            'string',
            'in:' . collect(static::options())->keys()->join(','),
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function options(): array
    {
        return [
            'strongly-agree' => 'Strongly agree',
            'agree' => 'Agree',
            'neutral' => 'Neither agree nor disagree',
            'disagree' => 'Disagree',
            'strongly-disagree' => 'Strongly disagree',
        ];
    }
}
