<?php

/*
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

    Advising App™ is licensed under the Elastic License 2.0. For more details,
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
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Advising App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace AdvisingApp\IntegrationOpenAi\Services\Concerns;

use CURLFile;
use Illuminate\Support\Collection;
use AdvisingApp\Ai\Models\AiMessage;
use AdvisingApp\Ai\Models\AiMessageFile;

trait UploadsFiles
{
    public function supportsFileUploads(): bool
    {
        return true;
    }

    public function createFiles(AiMessage $message, array $files): Collection
    {
        return collect($files)->map(function ($file) use ($message) {
            $fileRecord = new AiMessageFile();
            $fileRecord->temporary_url = $file['temporaryUrl'];
            $fileRecord->name = $file['name'];
            $fileRecord->mime_type = $file['mimeType'];

            $fileRecord->file_id = $this->uploadFileToClient($message, $fileRecord);

            return $fileRecord;
        });
    }

    protected function uploadFileToClient(AiMessage $message, AiMessageFile $file): string
    {
        $service = $message->thread->assistant->model->getService();
        ray('service', $service);

        $apiKey = $service->getApiKey();
        $apiVersion = $service->getApiVersion();

        $ch = curl_init();

        ray('service deployment', $service->getDeployment());

        curl_setopt($ch, CURLOPT_URL, $service->getDeployment() . '/files?api-version=' . $apiVersion);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);

        $cfile = new CURLFile($file->temporary_url, $file->mime_type, $file->name);

        $postFields = [
            'purpose' => 'assistants',
            'file' => $cfile,
        ];

        curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);

        $headers = [
            'api-key: ' . $apiKey,
            'OpenAI-Beta: assistants=v2',
            'Accept: */*',
            'Content-Type: multipart/form-data',
        ];

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        // Execute the cURL session
        $response = curl_exec($ch);

        ray(curl_getinfo($ch));
        ray('response', $response);

        $response = json_decode($response, true);
        ray('response', $response);

        if (curl_errno($ch) || ! isset($response['id'])) {
            if (! blank(curl_error($ch))) {
                throw new FileUploadException(curl_error($ch));
            }

            throw new FileUploadException();
        }

        // TODO Handle file upload status if not processed
        // $fileRecord->status = $response->status;

        curl_close($ch);

        return $response['id'];
    }
}
