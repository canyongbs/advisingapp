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
      of the licensor in the software. Any use of the licensor's trademarks is subject
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

use App\Features\MediaCreatedByFeature;
use App\Models\Media;
use App\Models\User;
use App\Observers\MediaObserver;
use Illuminate\Support\Facades\Auth;
use Laravel\Pennant\Feature;

describe('MediaObserver', function () {
    it('sets created_by on creating when feature is active and user is authenticated', function () {
        Feature::activate(MediaCreatedByFeature::class);

        $user = User::factory()->create();
        Auth::login($user);

        $media = new Media();
        $media->name = 'test';
        $media->file_name = 'test.txt';
        $media->collection_name = 'default';
        $media->disk = 's3';
        $media->manipulations = [];
        $media->custom_properties = [];
        $media->generated_conversions = [];
        $media->responsive_images = [];
        $media->size = 100;

        $observer = new MediaObserver();
        $observer->creating($media);

        expect($media->created_by_id)->toBe($user->getKey())
            ->and($media->created_by_type)->toBe($user->getMorphClass());
    });

    it('does not set created_by when feature is inactive', function () {
        Feature::deactivate(MediaCreatedByFeature::class);

        $user = User::factory()->create();
        Auth::login($user);

        $media = new Media();
        $media->name = 'test';
        $media->file_name = 'test.txt';

        $observer = new MediaObserver();
        $observer->creating($media);

        expect($media->created_by_id)->toBeNull()
            ->and($media->created_by_type)->toBeNull();
    });

    it('does not set created_by when no user is authenticated', function () {
        Feature::activate(MediaCreatedByFeature::class);

        $media = new Media();
        $media->name = 'test';
        $media->file_name = 'test.txt';

        $observer = new MediaObserver();
        $observer->creating($media);

        expect($media->created_by_id)->toBeNull()
            ->and($media->created_by_type)->toBeNull();
    });

    it('does not overwrite existing createdBy association', function () {
        Feature::activate(MediaCreatedByFeature::class);

        $user = User::factory()->create();
        $anotherUser = User::factory()->create();
        Auth::login($user);

        $media = new Media();
        $media->name = 'test';
        $media->file_name = 'test.txt';
        $media->created_by_id = $anotherUser->getKey();
        $media->created_by_type = $anotherUser->getMorphClass();

        $observer = new MediaObserver();
        $observer->creating($media);

        // Should NOT be overwritten
        expect($media->created_by_id)->toBe($anotherUser->getKey())
            ->and($media->created_by_type)->toBe($anotherUser->getMorphClass());
    });
});
