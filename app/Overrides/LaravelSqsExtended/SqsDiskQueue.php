<?php

declare(strict_types = 1);

namespace App\Overrides\LaravelSqsExtended;

use Illuminate\Support\Arr;
use Illuminate\Contracts\Queue\Job;
use Spatie\Multitenancy\Models\Tenant;
use Spatie\Multitenancy\Concerns\BindAsCurrentTenant;
use Spatie\Multitenancy\Models\Concerns\UsesTenantModel;
use DefectiveCode\LaravelSqsExtended\SqsDiskQueue as BaseSqsDiskQueue;
use Spatie\Multitenancy\Exceptions\CurrentTenantCouldNotBeDeterminedInTenantAwareJob;

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
