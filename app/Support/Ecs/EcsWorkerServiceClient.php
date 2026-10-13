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

namespace App\Support\Ecs;

use Aws\Ecs\EcsClient;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use UnexpectedValueException;

class EcsWorkerServiceClient
{
    private const int METADATA_TIMEOUT_SECONDS = 5;

    public function __construct(
        private EcsClient $ecs,
    ) {}

    /**
     * Null when not running on ECS, or when the task was not started by a service.
     */
    public function identifyCurrentTask(): ?EcsTaskIdentity
    {
        $metadataUri = Str::rtrim(Config::string('app.ecs_container_metadata_uri'), '/');

        if (blank($metadataUri)) {
            return null;
        }

        $metadata = Http::timeout(self::METADATA_TIMEOUT_SECONDS)
            ->acceptJson()
            ->get("{$metadataUri}/task")
            ->throw();

        $taskArn = $metadata->json('TaskARN');
        $cluster = $metadata->json('Cluster');

        assert(is_string($taskArn) && is_string($cluster));

        $task = $this->ecs->describeTasks(['cluster' => $cluster, 'tasks' => [$taskArn]])->search('tasks[0]');

        if (! is_array($task)) {
            return null;
        }

        $group = $task['group'] ?? null;
        $startedBy = $task['startedBy'] ?? null;

        if ((! is_string($group)) || (! str_starts_with($group, 'service:')) || (! is_string($startedBy))) {
            return null;
        }

        return new EcsTaskIdentity(
            taskArn: $taskArn,
            cluster: $cluster,
            serviceName: Str::after($group, 'service:'),
            startedBy: $startedBy,
        );
    }

    public function describeService(EcsTaskIdentity $task): EcsServiceState
    {
        $service = $this->ecs->describeServices(['cluster' => $task->cluster, 'services' => [$task->serviceName]])->search('services[0]');

        assert(is_array($service));

        $deployments = $service['deployments'] ?? [];

        assert(is_array($deployments));

        $primaryDeploymentId = null;

        foreach ($deployments as $deployment) {
            if (is_array($deployment) && (($deployment['status'] ?? null) === 'PRIMARY') && is_string($deployment['id'] ?? null)) {
                $primaryDeploymentId = $deployment['id'];
            }
        }

        // Every task would otherwise look older than the primary deployment and drain.
        if ($primaryDeploymentId === null) {
            throw new UnexpectedValueException('ECS described the worker service without a primary deployment.');
        }

        $desiredCount = $service['desiredCount'] ?? null;
        $runningCount = $service['runningCount'] ?? null;

        assert(is_int($desiredCount) && is_int($runningCount));

        return new EcsServiceState(
            primaryDeploymentId: $primaryDeploymentId,
            deploymentCount: count($deployments),
            desiredCount: $desiredCount,
            runningCount: $runningCount,
        );
    }

    /**
     * Tasks ECS intends to keep running; a task it is already stopping is not included.
     *
     * @return list<string>
     */
    public function runningTaskArns(EcsTaskIdentity $task): array
    {
        $runningTaskArns = [];

        $pages = $this->ecs->getPaginator('ListTasks', [
            'cluster' => $task->cluster,
            'serviceName' => $task->serviceName,
            'desiredStatus' => 'RUNNING',
        ]);

        foreach ($pages as $page) {
            foreach ($page['taskArns'] ?? [] as $taskArn) {
                if (is_string($taskArn)) {
                    $runningTaskArns[] = $taskArn;
                }
            }
        }

        return $runningTaskArns;
    }
}
