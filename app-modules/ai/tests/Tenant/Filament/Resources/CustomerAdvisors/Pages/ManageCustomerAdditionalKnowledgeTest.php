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

use AdvisingApp\Ai\Actions\UploadFileForParsing;
use AdvisingApp\Ai\DataTransferObjects\FileParsingResult;
use AdvisingApp\Ai\Filament\Resources\CustomerAdvisors\Pages\ManageCustomerAdditionalKnowledge;
use AdvisingApp\Ai\Models\CustomerAdvisor;
use AdvisingApp\Ai\Models\CustomerAdvisorFile;
use App\Features\LlamaParseFileParsingFeature;
use App\Settings\LicenseSettings;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Mockery\MockInterface;

use function Pest\Laravel\mock;

use Spatie\MediaLibrary\Downloaders\HttpFacadeDownloader;

use function Tests\asSuperAdmin;

beforeEach(function (): void {
    $settings = app(LicenseSettings::class);

    $settings->data->addons->customerAdvisors = true;

    $settings->save();

    asSuperAdmin();

    Queue::fake();
    Storage::fake('s3');

    // The uploaded file is attached from its temporary URL, so it is downloaded through the faked HTTP client.
    config(['media-library.media_downloader' => HttpFacadeDownloader::class]);

    Http::preventStrayRequests();
    Http::fake(['*' => Http::response('%PDF-1.4 Course syllabus')]);
});

$uploadFile = function (FileParsingResult $result): CustomerAdvisorFile {
    mock(UploadFileForParsing::class, fn (MockInterface $mock) => $mock->shouldReceive('execute')->once()->andReturn($result));

    $advisor = CustomerAdvisor::factory()->create();

    Livewire::test(ManageCustomerAdditionalKnowledge::class, ['record' => $advisor->getRouteKey()])
        ->fillForm(['uploaded_files' => [UploadedFile::fake()->create('syllabus.pdf', 10, 'application/pdf')]])
        ->call('save')
        ->assertHasNoFormErrors();

    return CustomerAdvisorFile::query()->whereBelongsTo($advisor, 'advisor')->sole();
};

it('records when parsing started for a file submitted to LlamaParse', function () use ($uploadFile): void {
    $file = $uploadFile(new FileParsingResult(fileId: 'pjb-123', parsingResults: null));

    expect($file->file_id)->toBe('pjb-123')
        ->and($file->parsing_started_at?->isAfter(now()->subMinute()))->toBeTrue();
});

it('does not record when parsing started for a file that was read directly', function () use ($uploadFile): void {
    $file = $uploadFile(new FileParsingResult(fileId: null, parsingResults: 'Office hours are on Tuesdays.'));

    expect($file->parsing_results)->toBe('Office hours are on Tuesdays.')
        ->and($file->parsing_started_at)->toBeNull();
});

// TODO: Cleanup Task (llamaparse-file-parsing): remove this test along with the feature flag.
it('does not record when parsing started while LlamaParse file parsing is inactive', function () use ($uploadFile): void {
    LlamaParseFileParsingFeature::deactivate();

    $file = $uploadFile(new FileParsingResult(fileId: '3b0b1f7e-2c6a-4f43-9d5f-1d2a2b7f9c10', parsingResults: null));

    expect($file->parsing_started_at)->toBeNull();
});
