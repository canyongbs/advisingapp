<?php

use function Tests\asSuperAdmin;
use function Pest\Livewire\livewire;

use AdvisingApp\Survey\Models\Survey;
use AdvisingApp\Survey\Filament\Resources\SurveyResource\Pages\ListSurveys;

it('can duplicate a survey and all of its content', function () {
    asSuperAdmin();

    // Given that we have a survey
    $survey = Survey::factory()->create();

    expect(Survey::count())->toBe(1);

    // And we duplicate it
    livewire(ListSurveys::class)
        ->assertStatus(200)
        ->callTableAction('Duplicate', $survey);

    // The survey, along with all of its content, should be duplicated
    expect(Survey::count())->toBe(2);
    expect(Survey::where('id', '<>', $survey->id)->first()->name)->toBe("Copy - {$survey->name}");
    expect(Survey::where('id', '<>', $survey->id)->first()->fields->count())->toBe($survey->fields->count());
    expect(Survey::where('id', '<>', $survey->id)->first()->steps->count())->toBe($survey->steps->count());
});
