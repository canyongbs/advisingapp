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

namespace Assist\LaravelAuditing\Contracts;

use Illuminate\Database\Eloquent\Relations\MorphMany;

interface Auditable
{
    /**
     * Auditable Model audits.
     *
     * @return MorphMany<\Assist\LaravelAuditing\Models\Audit>
     */
    public function audits(): MorphMany;

    /**
     * Set the Audit event.
     *
     * @param string $event
     *
     * @return Auditable
     */
    public function setAuditEvent(string $event): Auditable;

    /**
     * Get the Audit event that is set.
     *
     * @return string|null
     */
    public function getAuditEvent();

    /**
     * Get the events that trigger an Audit.
     *
     * @return array
     */
    public function getAuditEvents(): array;

    /**
     * Is the model ready for auditing?
     *
     * @return bool
     */
    public function readyForAuditing(): bool;

    /**
     * Return data for an Audit.
     *
     * @throws \Assist\LaravelAuditing\Exceptions\AuditingException
     *
     * @return array
     */
    public function toAudit(): array;

    /**
     * Get the (Auditable) attributes included in audit.
     *
     * @return array
     */
    public function getAuditInclude(): array;

    /**
     * Get the (Auditable) attributes excluded from audit.
     *
     * @return array
     */
    public function getAuditExclude(): array;

    /**
     * Get the strict audit status.
     *
     * @return bool
     */
    public function getAuditStrict(): bool;

    /**
     * Get the audit (Auditable) timestamps status.
     *
     * @return bool
     */
    public function getAuditTimestamps(): bool;

    /**
     * Get the Audit Driver.
     *
     * @return string|null
     */
    public function getAuditDriver();

    /**
     * Get the Audit threshold.
     *
     * @return int
     */
    public function getAuditThreshold(): int;

    /**
     * Get the Attribute modifiers.
     *
     * @return array
     */
    public function getAttributeModifiers(): array;

    /**
     * Transform the data before performing an audit.
     *
     * @param array $data
     *
     * @return array
     */
    public function transformAudit(array $data): array;

    /**
     * Generate an array with the model tags.
     *
     * @return array
     */
    public function generateTags(): array;

    /**
     * Transition to another model state from an Audit.
     *
     * @param Audit $audit
     * @param bool  $old
     *
     * @throws \Assist\LaravelAuditing\Exceptions\AuditableTransitionException
     *
     * @return Auditable
     */
    public function transitionTo(Audit $audit, bool $old = false): Auditable;
}
