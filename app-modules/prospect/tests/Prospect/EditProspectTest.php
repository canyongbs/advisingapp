<?php

use App\Models\User;

use function Pest\Laravel\actingAs;

use Assist\Prospect\Models\Prospect;

use function Pest\Livewire\livewire;

use Assist\Prospect\Filament\Resources\ProspectResource;
use Assist\Prospect\Tests\Prospect\RequestFactories\EditProspectRequestFactory;

// TODO: Write EditProspect page tests
//test('A successful action on the EditProspect page', function () {});
//
//test('EditProspect requires valid data', function ($data, $errors) {})->with([]);

// Permission Tests

test('EditProspect is gated with proper access control', function () {
    $user = User::factory()->create();

    $prospect = Prospect::factory()->create();

    actingAs($user)
        ->get(
            ProspectResource::getUrl('edit', [
                'record' => $prospect,
            ])
        )->assertForbidden();

    livewire(ProspectResource\Pages\EditProspect::class, [
        'record' => $prospect->getRouteKey(),
    ])
        ->assertForbidden();

    $user->givePermissionTo('prospect.view-any');
    $user->givePermissionTo('prospect.*.update');

    actingAs($user)
        ->get(
            ProspectResource::getUrl('edit', [
                'record' => $prospect,
            ])
        )->assertSuccessful();

    // TODO: Finish these tests to ensure changes are allowed
    $request = collect(EditProspectRequestFactory::new()->create());

    livewire(ProspectResource\Pages\EditProspect::class, [
        'record' => $prospect->getRouteKey(),
    ])
        ->fillForm($request->toArray())
        ->call('save')
        ->assertHasNoFormErrors();

    expect($prospect->fresh()->status_id)->toEqual($request->get('status_id'))
        ->and($prospect->fresh()->source_id)->toEqual($request->get('source_id'))
        ->and($prospect->fresh()->first_name)->toEqual($request->get('first_name'))
        ->and($prospect->fresh()->last_name)->toEqual($request->get('last_name'))
        ->and($prospect->fresh()->full)->toEqual($request->get('full'))
        ->and($prospect->fresh()->preferred)->toEqual($request->get('preferred'))
        ->and($prospect->fresh()->description)->toEqual($request->get('description'))
        ->and($prospect->fresh()->email)->toEqual($request->get('email'))
        ->and($prospect->fresh()->email_2)->toEqual($request->get('email_2'))
        ->and($prospect->fresh()->mobile)->toEqual($request->get('mobile'))
        ->and($prospect->fresh()->sms_opt_out)->toEqual($request->get('sms_opt_out'))
        ->and($prospect->fresh()->email_bounce)->toEqual($request->get('email_bounce'))
        ->and($prospect->fresh()->phone)->toEqual($request->get('phone'))
        ->and($prospect->fresh()->address)->toEqual($request->get('address'))
        ->and($prospect->fresh()->address_2)->toEqual($request->get('address_2'))
        ->and($prospect->fresh()->birthdate)->toEqual($request->get('birthdate'))
        ->and($prospect->fresh()->hsgrad)->toEqual($request->get('hsgrad'))
        ->and($prospect->fresh()->assigned_to_id)->toEqual($request->get('assigned_to_id'))
        ->and($prospect->fresh()->created_by_id)->toEqual($request->get('created_by_id'));
});
