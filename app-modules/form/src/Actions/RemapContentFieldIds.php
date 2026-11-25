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

namespace AdvisingApp\Form\Actions;

class RemapContentFieldIds
{
    /**
     * Points every custom block in rich content at a different field, such as the copy of the field on a duplicated record.
     *
     * @param array<string, mixed>|null $content
     * @param array<string, string> $fieldIdMap Original field ID => replacement field ID
     *
     * @return array<string, mixed>|null
     */
    public function __invoke(?array $content, array $fieldIdMap): ?array
    {
        if (blank($content) || ($fieldIdMap === [])) {
            return $content;
        }

        return $this->remapNode($content, $fieldIdMap);
    }

    /**
     * @param array<string, mixed> $node
     * @param array<string, string> $fieldIdMap
     *
     * @return array<string, mixed>
     */
    private function remapNode(array $node, array $fieldIdMap): array
    {
        $fieldId = $node['attrs']['config']['fieldId'] ?? null;

        if ((($node['type'] ?? null) === 'customBlock') && is_string($fieldId) && isset($fieldIdMap[$fieldId])) {
            $node['attrs']['config']['fieldId'] = $fieldIdMap[$fieldId];
        }

        if (is_array($node['content'] ?? null)) {
            $node['content'] = array_map(
                fn (mixed $child): mixed => is_array($child) ? $this->remapNode($child, $fieldIdMap) : $child,
                $node['content'],
            );
        }

        return $node;
    }
}
