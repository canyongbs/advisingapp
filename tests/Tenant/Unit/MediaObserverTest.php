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

use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\StudentDataModel\Models\Student;
use App\Models\Media;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

use function Pest\Laravel\actingAs;

describe('Media createdBy observer integration', function () {
    it('sets createdBy to the authenticated user on media upload', function () {
        Storage::fake('s3');

        $user = User::factory()->create();
        actingAs($user);

        $media = $user->addMediaFromString('test content')
            ->usingFileName('test.txt')
            ->toMediaCollection('default');

        expect($media->created_by_id)->toBe($user->getKey())
            ->and($media->created_by_type)->toBe($user->getMorphClass());
    });

    it('does not overwrite existing createdBy on media', function () {
        Storage::fake('s3');

        $user = User::factory()->create();
        $anotherUser = User::factory()->create();
        actingAs($user);

        $media = $user->addMediaFromString('test content')
            ->usingFileName('test.txt')
            ->toMediaCollection('default');

        // Manually set a different creator
        $media->createdBy()->associate($anotherUser);
        $media->saveQuietly();

        $media->refresh();

        expect($media->created_by_id)->toBe($anotherUser->getKey())
            ->and($media->created_by_type)->toBe($anotherUser->getMorphClass());
    });

    it('does not set createdBy on media when no user is authenticated', function () {
        Storage::fake('s3');

        $user = User::factory()->create();

        $media = $user->addMediaFromString('test content')
            ->usingFileName('test.txt')
            ->toMediaCollection('default');

        expect($media->created_by_id)->toBeNull()
            ->and($media->created_by_type)->toBeNull();
    });
});

describe('Media model createdBy relationship', function () {
    it('returns the user who created the media via createdBy relationship', function () {
        Storage::fake('s3');

        $user = User::factory()->create();
        actingAs($user);

        $media = $user->addMediaFromString('test content')
            ->usingFileName('test.txt')
            ->toMediaCollection('default');

        $media->refresh();

        expect($media->createdBy)->toBeInstanceOf(User::class)
            ->and($media->createdBy->getKey())->toBe($user->getKey());
    });

    it('returns student as media creator when manually associated', function () {
        Storage::fake('s3');

        $user = User::factory()->create();
        $student = Student::factory()->create();

        $media = $user->addMediaFromString('test content')
            ->usingFileName('test.txt')
            ->toMediaCollection('default');

        $media->createdBy()->associate($student);
        $media->saveQuietly();
        $media->refresh();

        expect($media->createdBy)->toBeInstanceOf(Student::class)
            ->and($media->createdBy->getKey())->toBe($student->getKey());
    });
});

describe('Media model accessor attributes', function () {
    it('returns user name from media created_by_name attribute', function () {
        Storage::fake('s3');

        $user = User::factory()->create(['name' => 'John Doe']);
        actingAs($user);

        $media = $user->addMediaFromString('test content')
            ->usingFileName('test.txt')
            ->toMediaCollection('default');

        $media->refresh();

        expect($media->created_by_name)->toBe('John Doe');
    });

    it('returns student name from media created_by_name attribute', function () {
        Storage::fake('s3');

        $user = User::factory()->create();
        $student = Student::factory()->create([
            'first' => 'Jane',
            'last' => 'Smith',
            'full_name' => 'Jane Smith',
        ]);

        $media = $user->addMediaFromString('test content')
            ->usingFileName('test.txt')
            ->toMediaCollection('default');

        $media->createdBy()->associate($student);
        $media->saveQuietly();
        $media->refresh();

        expect($media->created_by_name)->toBe('Jane Smith');
    });

    it('returns N/A for media created_by_name when no creator is set', function () {
        Storage::fake('s3');

        $user = User::factory()->create();

        $media = $user->addMediaFromString('test content')
            ->usingFileName('test.txt')
            ->toMediaCollection('default');

        expect($media->created_by_name)->toBe('N/A');
    });

    it('returns sub label for media creator user with job title', function () {
        Storage::fake('s3');

        $user = User::factory()->create([
            'name' => 'John Doe',
            'job_title' => 'Engineer',
        ]);
        actingAs($user);

        $media = $user->addMediaFromString('test content')
            ->usingFileName('test.txt')
            ->toMediaCollection('default');

        $media->refresh();

        expect($media->created_by_sub_label)->toContain('Engineer');
    });

    it('returns Student as media created_by_sub_label for student creator', function () {
        Storage::fake('s3');

        $user = User::factory()->create();
        $student = Student::factory()->create();

        $media = $user->addMediaFromString('test content')
            ->usingFileName('test.txt')
            ->toMediaCollection('default');

        $media->createdBy()->associate($student);
        $media->saveQuietly();
        $media->refresh();

        expect($media->created_by_sub_label)->toBe('Student');
    });

    it('returns Prospect as media created_by_sub_label for prospect creator', function () {
        Storage::fake('s3');

        $user = User::factory()->create();
        $prospect = Prospect::factory()->create();

        $media = $user->addMediaFromString('test content')
            ->usingFileName('test.txt')
            ->toMediaCollection('default');

        $media->createdBy()->associate($prospect);
        $media->saveQuietly();
        $media->refresh();

        expect($media->created_by_sub_label)->toBe('Prospect');
    });
});

describe('Media model config', function () {
    it('uses the custom Media model from config', function () {
        $mediaModel = config('media-library.media_model');

        expect($mediaModel)->toBe(Media::class);
    });

    it('Media model extends Spatie Media', function () {
        $media = new Media();

        expect($media)->toBeInstanceOf(Spatie\MediaLibrary\MediaCollections\Models\Media::class);
    });
});
