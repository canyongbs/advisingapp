<?php

use AdvisingApp\Form\Actions\GenerateSubmissibleValidation;
use AdvisingApp\Form\Actions\GenerateSubmissibleValidator;
use AdvisingApp\Form\Models\Form;
use AdvisingApp\Form\Models\FormField;
use Illuminate\Http\Request;

it('preserves zero-valued dropdown responses', function (bool $isRequired, bool $isWizard) {
    $form = Form::factory()
        ->has(FormField::factory()->state([
            'type' => 'select',
            'is_required' => $isRequired,
            'config' => ['options' => ['0%', '1%']],
        ]), 'fields')
        ->create(['is_wizard' => $isWizard, 'recaptcha_enabled' => false]);

    $field = $form->fields()->firstOrFail();
    $data = [$field->getKey() => '0'];

    if ($isWizard) {
        $step = $form->steps()->create(['label' => 'Choices', 'sort' => 1]);
        $field->step()->associate($step);
        $field->save();
        $data = [$step->label => $data];
    }

    $validator = (new GenerateSubmissibleValidator(
        Request::create('/', 'POST', $data),
        app(GenerateSubmissibleValidation::class),
    ))($form);

    expect($validator->passes())->toBeTrue()
        ->and($validator->validated())->toBe($data);
})->with([
    'required single-step field' => [true, false],
    'optional single-step field' => [false, false],
    'required multi-step field' => [true, true],
    'optional multi-step field' => [false, true],
]);

it('omits blank optional responses', function (mixed $response) {
    $form = Form::factory()
        ->has(FormField::factory()->state([
            'type' => 'select',
            'is_required' => false,
            'config' => ['options' => ['0%', '1%']],
        ]), 'fields')
        ->create(['is_wizard' => false, 'recaptcha_enabled' => false]);

    $field = $form->fields()->firstOrFail();

    $validator = (new GenerateSubmissibleValidator(
        Request::create('/', 'POST', [$field->getKey() => $response]),
        app(GenerateSubmissibleValidation::class),
    ))($form);

    expect($validator->passes())->toBeTrue()
        ->and($validator->validated())->toBe([]);
})->with([
    'null' => [null],
    'empty string' => [''],
    'empty array' => [[]],
]);
