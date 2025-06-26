<?php

use Illuminate\Database\Migrations\Migration;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('case_types', function (Blueprint $table) {
            $table->boolean('is_managers_case_created_email_enabled')->default(false);
            $table->boolean('is_managers_case_created_notification_enabled')->default(false);
            $table->boolean('is_managers_case_assigned_email_enabled')->default(false);
            $table->boolean('is_managers_case_assigned_notification_enabled')->default(false);
            $table->boolean('is_managers_case_closed_email_enabled')->default(false);
            $table->boolean('is_managers_case_closed_notification_enabled')->default(false);
            $table->boolean('is_auditors_case_created_email_enabled')->default(false);
            $table->boolean('is_auditors_case_created_notification_enabled')->default(false);
            $table->boolean('is_auditors_case_assigned_email_enabled')->default(false);
            $table->boolean('is_auditors_case_assigned_notification_enabled')->default(false);
            $table->boolean('is_auditors_case_closed_email_enabled')->default(false);
            $table->boolean('is_auditors_case_closed_notification_enabled')->default(false);
            $table->boolean('is_managers_case_update_email_enabled')->default(false);
            $table->boolean('is_managers_case_update_notification_enabled')->default(false);
            $table->boolean('is_managers_case_status_change_email_enabled')->default(false);
            $table->boolean('is_managers_case_status_change_notification_enabled')->default(false);
            $table->boolean('is_auditors_case_update_email_enabled')->default(false);
            $table->boolean('is_auditors_case_update_notification_enabled')->default(false);
            $table->boolean('is_auditors_case_status_change_email_enabled')->default(false);
            $table->boolean('is_auditors_case_status_change_notification_enabled')->default(false);
            $table->boolean('is_customers_case_created_email_enabled')->default(false);
            $table->boolean('is_customers_case_created_notification_enabled')->default(false);
            $table->boolean('is_customers_case_assigned_email_enabled')->default(false);
            $table->boolean('is_customers_case_assigned_notification_enabled')->default(false);
            $table->boolean('is_customers_case_update_email_enabled')->default(false);
            $table->boolean('is_customers_case_update_notification_enabled')->default(false);
            $table->boolean('is_customers_case_status_change_email_enabled')->default(false);
            $table->boolean('is_customers_case_status_change_notification_enabled')->default(false);
            $table->boolean('is_customers_case_closed_email_enabled')->default(false);
            $table->boolean('is_customers_case_closed_notification_enabled')->default(false);
            $table->boolean('is_customers_survey_response_email_enabled')->default(false);
        });
    }

    public function down(): void
    {
        Schema::table('case_types', function (Blueprint $table) {
            $table->dropColumn([
                'is_managers_case_created_email_enabled',
                'is_managers_case_created_notification_enabled',
                'is_managers_case_assigned_email_enabled',
                'is_managers_case_assigned_notification_enabled',
                'is_managers_case_closed_email_enabled',
                'is_managers_case_closed_notification_enabled',
                'is_auditors_case_created_email_enabled',
                'is_auditors_case_created_notification_enabled',
                'is_auditors_case_assigned_email_enabled',
                'is_auditors_case_assigned_notification_enabled',
                'is_auditors_case_closed_email_enabled',
                'is_auditors_case_closed_notification_enabled',
                'is_managers_case_update_email_enabled',
                'is_managers_case_update_notification_enabled',
                'is_managers_case_status_change_email_enabled',
                'is_managers_case_status_change_notification_enabled',
                'is_auditors_case_update_email_enabled',
                'is_auditors_case_update_notification_enabled',
                'is_auditors_case_status_change_email_enabled',
                'is_auditors_case_status_change_notification_enabled',
                'is_customers_case_created_email_enabled',
                'is_customers_case_created_notification_enabled',
                'is_customers_case_assigned_email_enabled',
                'is_customers_case_assigned_notification_enabled',
                'is_customers_case_update_email_enabled',
                'is_customers_case_update_notification_enabled',
                'is_customers_case_status_change_email_enabled',
                'is_customers_case_status_change_notification_enabled',
                'is_customers_case_closed_email_enabled',
                'is_customers_case_closed_notification_enabled',
                'is_customers_survey_response_email_enabled',
            ]);
        });
    }
};
