<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

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

namespace AdvisingApp\Workflow\Filament\Blocks;

use AdvisingApp\Workflow\Models\WorkflowDetails;
use Filament\Forms\ComponentContainer;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\Section;

abstract class WorkflowActionBlock extends Block
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    public static function make(?string $name = null): static
    {
        return parent::make($name ?? static::type());
    }

    /**
     * @return array<int, covariant Field | Section>
     */
    public function createFields(): array
    {
        return $this->generateFields();
    }

    /**
     * @return array<int, covariant Field | Section>
     */
    public function editFields(): array
    {
        return $this->generateFields();
    }

    /**
     * @return array<int, covariant Field | Section>
     */
    abstract public function generateFields(): array;

    abstract public static function type(): string;

    public function afterCreated(WorkflowDetails $action, ComponentContainer $componentContainer): void {}

    /**
     * @param array<string, mixed> $data
     *
     * @return array<string, mixed>
     */
    public function beforeCreate(array $data): array
    {
        return $data;
    }

    /**
     * @param array<string, mixed> $data
     *
     * @return array<string, mixed>
     */
    public function beforeUpdate(array $data): array
    {
        return $data;
    }

    /**
     * Prepare WorkflowDetails for form population during editing.
     * Allows blocks to load necessary relationships before toArray() is called.
     */
    public function prepareForEdit(WorkflowDetails $details): void {}
}
