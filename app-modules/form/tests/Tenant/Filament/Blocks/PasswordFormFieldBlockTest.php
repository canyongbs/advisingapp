<?php

use AdvisingApp\Form\Filament\Blocks\FormFieldBlockRegistry;
use AdvisingApp\Form\Filament\Blocks\PasswordFormFieldBlock;
use AdvisingApp\Form\Models\FormField;
use AdvisingApp\Form\Models\Secret;
use App\Features\PasswordFormFieldFeature;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

it('allows an empty value when the field is optional', function (): void {
    $rules = PasswordFormFieldBlock::getValidationRules(new FormField(['type' => 'password']));

    expect(Validator::make(['password' => null], ['password' => $rules])->passes())->toBeTrue();
});

it('only accepts existing unattached secrets', function (): void {
    $rules = PasswordFormFieldBlock::getValidationRules(new FormField(['type' => 'password']));

    $unattached = Secret::factory()->create();
    $attached = Secret::factory()->for(Secret::factory()->create(), 'related')->create();

    expect(Validator::make(['password' => $unattached->getKey()], ['password' => $rules])->passes())->toBeTrue()
        ->and(Validator::make(['password' => $attached->getKey()], ['password' => $rules])->passes())->toBeFalse()
        ->and(Validator::make(['password' => (string) Str::uuid()], ['password' => $rules])->passes())->toBeFalse()
        ->and(Validator::make(['password' => 'plain-text-password'], ['password' => $rules])->passes())->toBeFalse();
});

it('is only offered when the feature is active', function (): void {
    PasswordFormFieldFeature::deactivate();

    expect(FormFieldBlockRegistry::keyByType())->not->toHaveKey('password');

    PasswordFormFieldFeature::activate();

    expect(FormFieldBlockRegistry::keyByType())->toHaveKey('password')
        ->and(FormFieldBlockRegistry::keyByTypeForEvents())->toHaveKey('password');
});
