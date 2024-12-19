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

declare(strict_types = 1);

namespace App\Overrides\LaravelSqsExtended;

use DefectiveCode\LaravelSqsExtended\SqsDiskJob;
use DefectiveCode\LaravelSqsExtended\SqsDiskQueue as BaseSqsDiskQueue;
use Illuminate\Contracts\Queue\Job;
use Illuminate\Support\Arr;
use Spatie\Multitenancy\Concerns\BindAsCurrentTenant;
use Spatie\Multitenancy\Exceptions\CurrentTenantCouldNotBeDeterminedInTenantAwareJob;
use Spatie\Multitenancy\Models\Concerns\UsesTenantModel;
use Spatie\Multitenancy\Models\Tenant;

class SqsDiskQueue extends BaseSqsDiskQueue
{
    use BindAsCurrentTenant;
    use UsesTenantModel;

    /**
     * Push a raw payload onto the queue.
     *
     * @param  string  $payload
     * @param  string|null  $queue
     * @param  mixed  $delay
     *
     * @return mixed
     */
    public function pushRaw($payload, $queue = null, array $options = [], $delay = 0)
    {
        $message = [
            'QueueUrl' => $this->getQueue($queue),
            'MessageBody' => $payload,
        ];

        if (strlen($payload) >= self::MAX_SQS_LENGTH || Arr::get($this->diskOptions, 'always_store')) {
            $decodedPayload = json_decode($payload);

            $uuid = $decodedPayload->uuid;
            $filepath = Arr::get($this->diskOptions, 'prefix', '') . "/{$uuid}.json";
            $this->resolveDisk()->put($filepath, $payload);

            $message['MessageBody'] = json_encode([
                'pointer' => $filepath,
                ...(isset($decodedPayload->tenantId) ? ['tenantId' => $decodedPayload->tenantId] : []),
            ]);
        }

        if ($delay) {
            $message['DelaySeconds'] = $this->secondsUntil($delay);
        }

        return $this->sqs->sendMessage($message)->get('MessageId');
    }

    /**
     * Pop the next job off of the queue.
     *
     * @param  string|null  $queue
     *
     * @return Job|null
     */
    public function pop($queue = null)
    {
        $response = $this->sqs->receiveMessage([
            'QueueUrl' => $queue = $this->getQueue($queue),
            'AttributeNames' => ['ApproximateReceiveCount'],
        ]);

        if (! is_null($response['Messages']) && count($response['Messages']) > 0) {
            if (isset(json_decode($response['Messages'][0]['Body'])->tenantId)) {
                /** @var Tenant $tenant */
                $tenant = $this->getTenantModel()::find(json_decode($response['Messages'][0]['Body'])->tenantId);

                if (! $tenant) {
                    throw new CurrentTenantCouldNotBeDeterminedInTenantAwareJob('The current tenant could not be determined in a job. The tenant finder could not find a tenant.');
                }

                $this->bindAsCurrentTenant($tenant->makeCurrent());
            }

            return new SqsDiskJob(
                $this->container,
                $this->sqs,
                $response['Messages'][0],
                $this->connectionName,
                $queue,
                $this->diskOptions
            );
        }
    }
}
