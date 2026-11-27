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

use AdvisingApp\Form\Actions\FindMissingProspectGenerationFields;
use AdvisingApp\Form\Enums\ProspectGenerationField;
use AdvisingApp\Form\Filament\Blocks\EducatableEmailFormFieldBlock;
use AdvisingApp\Form\Filament\Blocks\EducatableNameFormFieldBlock;

$emailBlock = fn (bool $isRequired = true): array => [
    'type' => 'customBlock',
    'attrs' => ['id' => EducatableEmailFormFieldBlock::type(), 'config' => ['isRequired' => $isRequired]],
];

$nameBlock = fn (): array => [
    'type' => 'customBlock',
    'attrs' => ['id' => EducatableNameFormFieldBlock::type(), 'config' => []],
];

$document = fn (array ...$nodes): array => ['type' => 'doc', 'content' => $nodes];

it('finds nothing missing when a required email and a name are present', function () use ($emailBlock, $nameBlock, $document): void {
    $missingFields = app(FindMissingProspectGenerationFields::class)([$document($emailBlock(), $nameBlock())]);

    expect($missingFields)->toBe([]);
});

it('reports the email as missing when it is not required', function () use ($emailBlock, $nameBlock, $document): void {
    $missingFields = app(FindMissingProspectGenerationFields::class)([$document($emailBlock(isRequired: false), $nameBlock())]);

    expect($missingFields)->toBe([ProspectGenerationField::Email]);
});

it('reports each missing field', function () use ($emailBlock, $nameBlock, $document): void {
    $action = app(FindMissingProspectGenerationFields::class);

    expect($action([$document($nameBlock())]))->toBe([ProspectGenerationField::Email])
        ->and($action([$document($emailBlock())]))->toBe([ProspectGenerationField::Name])
        ->and($action([$document(['type' => 'paragraph'])]))->toBe([ProspectGenerationField::Email, ProspectGenerationField::Name]);
});

it('finds fields nested in a grid', function () use ($emailBlock, $nameBlock, $document): void {
    $grid = [
        'type' => 'grid',
        'content' => [
            ['type' => 'gridColumn', 'content' => [$emailBlock()]],
            ['type' => 'gridColumn', 'content' => [$nameBlock()]],
        ],
    ];

    expect(app(FindMissingProspectGenerationFields::class)([$document($grid)]))->toBe([]);
});

it('finds fields spread across multiple documents', function () use ($emailBlock, $nameBlock, $document): void {
    $missingFields = app(FindMissingProspectGenerationFields::class)([
        $document($emailBlock()),
        json_encode($document($nameBlock())),
    ]);

    expect($missingFields)->toBe([]);
});

it('reports both fields as missing for empty content', function (array $documents): void {
    expect(app(FindMissingProspectGenerationFields::class)($documents))
        ->toBe([ProspectGenerationField::Email, ProspectGenerationField::Name]);
})->with([
    'no documents' => [[]],
    'null document' => [[null]],
    'document without content' => [[['type' => 'doc']]],
]);
