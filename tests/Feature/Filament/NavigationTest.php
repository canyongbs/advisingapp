<?php

use Illuminate\Support\Arr;
use Filament\Facades\Filament;

test('there is only the Dashboard item for unlicensed users', function () {
    $this->actingAs(User::factory()->create());

    $navigation = Filament::getNavigation();

    $this->assertCount(1, $navigation);
    $this->assertCount(1, Arr::first($navigation)->getItems());
});
