<?php

/*
<COPYRIGHT>

    Copyright © 2022-2023, Canyon GBS LLC. All rights reserved.

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

namespace Assist\LaravelAuditing;

use InvalidArgumentException;
use Illuminate\Support\Manager;
use Illuminate\Support\Facades\Config;
use Assist\LaravelAuditing\Events\Audited;
use Assist\LaravelAuditing\Events\Auditing;
use Assist\LaravelAuditing\Drivers\Database;
use Assist\LaravelAuditing\Contracts\Auditable;
use Assist\LaravelAuditing\Contracts\AuditDriver;
use Assist\LaravelAuditing\Exceptions\AuditingException;

class Auditor extends Manager implements Contracts\Auditor
{
    /**
     * {@inheritdoc}
     */
    public function getDefaultDriver()
    {
        return 'database';
    }

    /**
     * {@inheritdoc}
     */
    public function auditDriver(Auditable $model): AuditDriver
    {
        $driver = $this->driver($model->getAuditDriver());

        if (! $driver instanceof AuditDriver) {
            throw new AuditingException('The driver must implement the AuditDriver contract');
        }

        return $driver;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(Auditable $model): void
    {
        if (! $model->readyForAuditing()) {
            return;
        }

        $driver = $this->auditDriver($model);

        if (! $this->fireAuditingEvent($model, $driver)) {
            return;
        }

        // Check if we want to avoid storing empty values
        $allowEmpty = Config::get('audit.empty_values');
        $explicitAllowEmpty = in_array($model->getAuditEvent(), Config::get('audit.allowed_empty_values', []));

        if (! $allowEmpty && ! $explicitAllowEmpty) {
            if (
                empty($model->toAudit()['new_values']) &&
                empty($model->toAudit()['old_values'])
            ) {
                return;
            }
        }

        $audit = $driver->audit($model);

        if (! $audit) {
            return;
        }

        $driver->prune($model);

        $this->container->make('events')->dispatch(
            new Audited($model, $driver, $audit)
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function createDriver($driver)
    {
        try {
            return parent::createDriver($driver);
        } catch (InvalidArgumentException $exception) {
            if (class_exists($driver)) {
                return $this->container->make($driver);
            }

            throw $exception;
        }
    }

    /**
     * Create an instance of the Database audit driver.
     *
     * @return \Assist\LaravelAuditing\Drivers\Database
     */
    protected function createDatabaseDriver(): Database
    {
        return $this->container->make(Database::class);
    }

    /**
     * Fire the Auditing event.
     *
     * @param \Assist\LaravelAuditing\Contracts\Auditable $model
     * @param \Assist\LaravelAuditing\Contracts\AuditDriver $driver
     *
     * @return bool
     */
    protected function fireAuditingEvent(Auditable $model, AuditDriver $driver): bool
    {
        return $this
            ->container
            ->make('events')
            ->until(new Auditing($model, $driver)) !== false;
    }
}
