<?php

namespace AdvisingApp\Project\Tests\Tenant\Filament\Resources\ProjectResource\RequestFactory;

use Worksome\RequestFactories\RequestFactory;

class EditProjectRequestFactory extends RequestFactory
{
  public function definition(): array
  {
    return [
      'name' => str($this->faker->unique()->words(3, true))->title()->toString(),
      'description' => $this->faker->paragraph(),
    ];
  }
}
