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

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        DB::transaction(function () {
            DB::table('permissions')
                ->where('name', 'LIKE', 'knowledge_base_article.%')
                ->update([
                    'name' => DB::raw("REPLACE(name, 'knowledge_base_article.', 'resource_hub_article.')"),
                    'updated_at' => now(),
                ]);

            DB::table('permission_groups')
                ->where('name', 'Knowledge Base Item')
                ->update([
                    'name' => 'Resource Hub Article',
                    'updated_at' => now(),
                ]);

            DB::table('permissions')
                ->where('name', 'LIKE', 'knowledge_base_category.%')
                ->update([
                    'name' => DB::raw("REPLACE(name, 'knowledge_base_category.', 'resource_hub_category.')"),
                    'updated_at' => now(),
                ]);

            DB::table('permission_groups')
                ->where('name', 'Knowledge Base Category')
                ->update([
                    'name' => 'Resource Hub Category',
                    'updated_at' => now(),
                ]);

            DB::table('permissions')
                ->where('name', 'LIKE', 'knowledge_base_quality.%')
                ->update([
                    'name' => DB::raw("REPLACE(name, 'knowledge_base_quality.', 'resource_hub_quality.')"),
                    'updated_at' => now(),
                ]);

            DB::table('permission_groups')
                ->where('name', 'Knowledge Base Quality')
                ->update([
                    'name' => 'Resource Hub Quality',
                    'updated_at' => now(),
                ]);

            DB::table('permissions')
                ->where('name', 'LIKE', 'knowledge_base_status.%')
                ->update([
                    'name' => DB::raw("REPLACE(name, 'knowledge_base_status.', 'resource_hub_status.')"),
                    'updated_at' => now(),
                ]);

            DB::table('permission_groups')
                ->where('name', 'Knowledge Base Status')
                ->update([
                    'name' => 'Resource Hub Status',
                    'updated_at' => now(),
                ]);
        });
    }

    public function down(): void
    {
        DB::transaction(function () {
            DB::table('permissions')
                ->where('name', 'LIKE', 'resource_hub_article.%')
                ->update([
                    'name' => DB::raw("REPLACE(name, 'resource_hub_article.', 'knowledge_base_article.')"),
                    'updated_at' => now(),
                ]);

            DB::table('permission_groups')
                ->where('name', 'Resource Hub Article')
                ->update([
                    'name' => 'Knowledge Base Item',
                    'updated_at' => now(),
                ]);

            DB::table('permissions')
                ->where('name', 'LIKE', 'resource_hub_category.%')
                ->update([
                    'name' => DB::raw("REPLACE(name, 'resource_hub_category.', 'knowledge_base_category.')"),
                    'updated_at' => now(),
                ]);

            DB::table('permission_groups')
                ->where('name', 'Resource Hub Category')
                ->update([
                    'name' => 'Knowledge Base Category',
                    'updated_at' => now(),
                ]);

            DB::table('permissions')
                ->where('name', 'LIKE', 'resource_hub_quality.%')
                ->update([
                    'name' => DB::raw("REPLACE(name, 'resource_hub_quality.', 'knowledge_base_quality.')"),
                    'updated_at' => now(),
                ]);

            DB::table('permission_groups')
                ->where('name', 'Resource Hub Quality')
                ->update([
                    'name' => 'Knowledge Base Quality',
                    'updated_at' => now(),
                ]);

            DB::table('permissions')
                ->where('name', 'LIKE', 'resource_hub_status.%')
                ->update([
                    'name' => DB::raw("REPLACE(name, 'resource_hub_status.', 'knowledge_base_status.')"),
                    'updated_at' => now(),
                ]);

            DB::table('permission_groups')
                ->where('name', 'Resource Hub Status')
                ->update([
                    'name' => 'Knowledge Base Status',
                    'updated_at' => now(),
                ]);
        });
    }
};
