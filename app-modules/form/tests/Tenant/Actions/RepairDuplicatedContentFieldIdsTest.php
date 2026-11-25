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

use AdvisingApp\Form\Actions\RepairDuplicatedContentFieldIds;
use AdvisingApp\Form\Models\Form;
use AdvisingApp\Form\Models\FormField;
use AdvisingApp\Form\Models\FormStep;
use AdvisingApp\Form\Models\SubmissibleField;
use Illuminate\Support\Facades\Log;

use function Pest\Laravel\travel;

$customBlock = fn (SubmissibleField $field): array => [
    'type' => 'customBlock',
    'attrs' => [
        'id' => $field->type,
        'config' => [
            'fieldId' => $field->getKey(),
            'label' => $field->label,
            'isRequired' => $field->is_required,
            ...$field->config,
        ],
    ],
];

$grid = fn (array $blocks): array => [
    'type' => 'grid',
    'attrs' => ['data-cols' => count($blocks)],
    'content' => array_map(fn (array $block): array => ['type' => 'gridColumn', 'content' => [$block]], $blocks),
];

$referencedFieldIds = function (?array $content): array {
    preg_match_all('/"fieldId":"([^"]+)"/', json_encode($content ?? []), $matches);

    return $matches[1];
};

/**
 * Duplicates a form the way duplication did before it remapped content field IDs: fields are copied, content is not remapped.
 */
$duplicateWithoutRemapping = function (Form $original): Form {
    $replica = $original->replicate();
    $replica->name = "Copy - {$original->name}";
    $replica->setAttribute('root_id', null);
    $replica->save();

    $stepMap = [];

    foreach ($original->steps as $step) {
        assert($step instanceof FormStep);

        $replicaStep = $step->replicate();
        $replicaStep->form_id = $replica->getKey();
        $replicaStep->save();

        $stepMap[$step->getKey()] = $replicaStep->getKey();
    }

    foreach ($original->fields as $field) {
        assert($field instanceof FormField);

        $replicaField = $field->replicate();
        $replicaField->form_id = $replica->getKey();
        $replicaField->step_id = $stepMap[$field->step_id] ?? null;
        $replicaField->save();
    }

    return $replica->refresh();
};

it('repairs single-page content that references the original form fields', function () use ($customBlock, $grid, $referencedFieldIds, $duplicateWithoutRemapping): void {
    $original = Form::factory()->create();

    [$firstField, $secondField, $thirdField] = $original->fields->all();

    $original->update(['content' => [
        'type' => 'doc',
        'content' => [$customBlock($firstField), $grid([$customBlock($secondField), $customBlock($thirdField)])],
    ]]);

    $replica = $duplicateWithoutRemapping($original);

    expect($referencedFieldIds($replica->content))->toEqualCanonicalizing($original->fields->modelKeys());

    $repairedCount = app(RepairDuplicatedContentFieldIds::class)($replica);

    expect($repairedCount)->toBe(3)
        ->and($referencedFieldIds($replica->refresh()->content))->toEqualCanonicalizing($replica->fields->modelKeys())
        ->and($referencedFieldIds($original->refresh()->content))->toEqualCanonicalizing($original->fields->modelKeys());
});

it('repairs step content that references the original form fields', function () use ($customBlock, $referencedFieldIds, $duplicateWithoutRemapping): void {
    $original = Form::factory()->create(['is_wizard' => true]);
    $original->fields()->delete();
    $original->update(['content' => null]);

    foreach (['First step', 'Second step'] as $sort => $label) {
        $step = $original->steps()->create(['label' => $label, 'sort' => $sort]);

        $field = $step->fields()->create([...FormField::factory()->make()->toArray(), 'form_id' => $original->getKey()]);

        $step->update(['content' => ['type' => 'doc', 'content' => [$customBlock($field)]]]);
    }

    $replica = $duplicateWithoutRemapping($original->refresh());

    $repairedCount = app(RepairDuplicatedContentFieldIds::class)($replica);

    expect($repairedCount)->toBe(2);

    foreach ($replica->steps()->get() as $replicaStep) {
        expect($referencedFieldIds($replicaStep->content))->toBe($replicaStep->fields()->pluck('id')->all());
    }
});

it('repairs identical blocks to different fields', function () use ($customBlock, $referencedFieldIds, $duplicateWithoutRemapping): void {
    $original = Form::factory()->create();
    $original->fields()->delete();

    $fieldAttributes = ['label' => 'Comments', 'type' => 'text_input', 'is_required' => false, 'config' => []];
    $firstField = $original->fields()->create($fieldAttributes);
    $secondField = $original->fields()->create($fieldAttributes);

    $original->update(['content' => ['type' => 'doc', 'content' => [$customBlock($firstField), $customBlock($secondField)]]]);

    $replica = $duplicateWithoutRemapping($original->refresh());

    $repairedCount = app(RepairDuplicatedContentFieldIds::class)($replica);

    expect($repairedCount)->toBe(2)
        ->and($referencedFieldIds($replica->refresh()->content))->toEqualCanonicalizing($replica->fields->modelKeys());
});

it('does not change content that already references its own fields', function (): void {
    $form = Form::factory()->create();
    $originalContent = $form->content;
    $originalUpdatedAt = $form->updated_at;

    travel(1)->minute();

    $repairedCount = app(RepairDuplicatedContentFieldIds::class)($form);

    expect($repairedCount)->toBe(0)
        ->and($form->refresh()->content)->toEqual($originalContent)
        ->and($form->updated_at->equalTo($originalUpdatedAt))->toBeTrue();
});

it('does not repair a block that no unclaimed field matches', function () use ($referencedFieldIds, $duplicateWithoutRemapping): void {
    $log = Log::spy();

    $original = Form::factory()->create();
    $originalFieldIds = $referencedFieldIds($original->content);

    $replica = $duplicateWithoutRemapping($original);
    $replica->fields()->update(['label' => 'A label no block has']);

    $repairedCount = app(RepairDuplicatedContentFieldIds::class)($replica);

    expect($repairedCount)->toBe(0)
        ->and($referencedFieldIds($replica->refresh()->content))->toBe($originalFieldIds);

    $log->shouldHaveReceived('warning')->times(count($originalFieldIds));
});
