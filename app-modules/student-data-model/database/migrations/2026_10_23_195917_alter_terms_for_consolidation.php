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

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        DB::transaction(function (): void {
            DB::statement('ALTER TABLE terms ALTER COLUMN name TYPE citext');

            Schema::table('terms', function (Blueprint $table): void {
                $table->string('sis_term_id')->nullable()->change();
                $table->boolean('is_active')->default(true)->change();
                $table->unsignedInteger('order')->nullable();
                $table->timestamps();
                $table->softDeletes();

                $table->index('name');
            });

            // Links each enrollment to one term: by SIS term ID, otherwise by name ignoring case, preferring SIS terms, then the latest start date.
            DB::statement(<<<'SQL'
                CREATE VIEW enrollment_terms AS
                SELECT
                    enrollments.id AS enrollment_id,
                    resolved_terms.id AS term_id
                FROM enrollments
                JOIN LATERAL (
                    SELECT terms.id
                    FROM terms
                    WHERE terms.deleted_at IS NULL
                        AND (
                            terms.sis_term_id = enrollments.sis_term_id
                            OR terms.name = enrollments.semester_name::citext
                        )
                    ORDER BY
                        (terms.sis_term_id = enrollments.sis_term_id) DESC NULLS LAST,
                        (terms.sis_term_id IS NOT NULL) DESC,
                        terms.start_date DESC NULLS LAST,
                        terms.id
                    LIMIT 1
                ) AS resolved_terms ON true
            SQL);
        });
    }

    public function down(): void
    {
        DB::transaction(function (): void {
            DB::statement('DROP VIEW IF EXISTS enrollment_terms');

            DB::table('terms')->whereNull('sis_term_id')->delete();

            Schema::table('terms', function (Blueprint $table): void {
                $table->dropIndex(['name']);
                $table->dropSoftDeletes();
                $table->dropTimestamps();
                $table->dropColumn('order');
                $table->boolean('is_active')->default(null)->change();
                $table->string('sis_term_id')->nullable(false)->change();
            });

            DB::statement('ALTER TABLE terms ALTER COLUMN name TYPE varchar(255)');
        });
    }
};
