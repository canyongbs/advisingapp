<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionsTableSeeder extends Seeder
{
    public function run()
    {
        $permissions = [
            [
                'id'    => 1,
                'title' => 'auth_profile_edit',
            ],
            [
                'id'    => 2,
                'title' => 'user_management_access',
            ],
            [
                'id'    => 3,
                'title' => 'permission_create',
            ],
            [
                'id'    => 4,
                'title' => 'permission_edit',
            ],
            [
                'id'    => 5,
                'title' => 'permission_show',
            ],
            [
                'id'    => 6,
                'title' => 'permission_delete',
            ],
            [
                'id'    => 7,
                'title' => 'permission_access',
            ],
            [
                'id'    => 8,
                'title' => 'role_create',
            ],
            [
                'id'    => 9,
                'title' => 'role_edit',
            ],
            [
                'id'    => 10,
                'title' => 'role_show',
            ],
            [
                'id'    => 11,
                'title' => 'role_delete',
            ],
            [
                'id'    => 12,
                'title' => 'role_access',
            ],
            [
                'id'    => 13,
                'title' => 'user_create',
            ],
            [
                'id'    => 14,
                'title' => 'user_edit',
            ],
            [
                'id'    => 15,
                'title' => 'user_show',
            ],
            [
                'id'    => 16,
                'title' => 'user_delete',
            ],
            [
                'id'    => 17,
                'title' => 'user_access',
            ],
            [
                'id'    => 18,
                'title' => 'audit_log_show',
            ],
            [
                'id'    => 19,
                'title' => 'audit_log_access',
            ],
            [
                'id'    => 20,
                'title' => 'user_alert_create',
            ],
            [
                'id'    => 21,
                'title' => 'user_alert_edit',
            ],
            [
                'id'    => 22,
                'title' => 'user_alert_show',
            ],
            [
                'id'    => 23,
                'title' => 'user_alert_delete',
            ],
            [
                'id'    => 24,
                'title' => 'user_alert_access',
            ],
            [
                'id'    => 25,
                'title' => 'record_menu_access',
            ],
            [
                'id'    => 26,
                'title' => 'engage_menu_access',
            ],
            [
                'id'    => 27,
                'title' => 'case_menu_access',
            ],
            [
                'id'    => 28,
                'title' => 'support_menu_access',
            ],
            [
                'id'    => 29,
                'title' => 'settings_menu_access',
            ],
            [
                'id'    => 30,
                'title' => 'institution_create',
            ],
            [
                'id'    => 31,
                'title' => 'institution_edit',
            ],
            [
                'id'    => 32,
                'title' => 'institution_show',
            ],
            [
                'id'    => 33,
                'title' => 'institution_delete',
            ],
            [
                'id'    => 34,
                'title' => 'institution_access',
            ],
            [
                'id'    => 35,
                'title' => 'case_item_create',
            ],
            [
                'id'    => 36,
                'title' => 'case_item_edit',
            ],
            [
                'id'    => 37,
                'title' => 'case_item_show',
            ],
            [
                'id'    => 38,
                'title' => 'case_item_delete',
            ],
            [
                'id'    => 39,
                'title' => 'case_item_access',
            ],
            [
                'id'    => 40,
                'title' => 'case_item_status_create',
            ],
            [
                'id'    => 41,
                'title' => 'case_item_status_edit',
            ],
            [
                'id'    => 42,
                'title' => 'case_item_status_show',
            ],
            [
                'id'    => 43,
                'title' => 'case_item_status_delete',
            ],
            [
                'id'    => 44,
                'title' => 'case_item_status_access',
            ],
            [
                'id'    => 45,
                'title' => 'case_item_type_create',
            ],
            [
                'id'    => 46,
                'title' => 'case_item_type_edit',
            ],
            [
                'id'    => 47,
                'title' => 'case_item_type_show',
            ],
            [
                'id'    => 48,
                'title' => 'case_item_type_delete',
            ],
            [
                'id'    => 49,
                'title' => 'case_item_type_access',
            ],
            [
                'id'    => 50,
                'title' => 'case_item_priority_create',
            ],
            [
                'id'    => 51,
                'title' => 'case_item_priority_edit',
            ],
            [
                'id'    => 52,
                'title' => 'case_item_priority_show',
            ],
            [
                'id'    => 53,
                'title' => 'case_item_priority_delete',
            ],
            [
                'id'    => 54,
                'title' => 'case_item_priority_access',
            ],
            [
                'id'    => 55,
                'title' => 'kb_menu_access',
            ],
            [
                'id'    => 56,
                'title' => 'kb_item_create',
            ],
            [
                'id'    => 57,
                'title' => 'kb_item_edit',
            ],
            [
                'id'    => 58,
                'title' => 'kb_item_show',
            ],
            [
                'id'    => 59,
                'title' => 'kb_item_delete',
            ],
            [
                'id'    => 60,
                'title' => 'kb_item_access',
            ],
            [
                'id'    => 61,
                'title' => 'kb_item_quality_create',
            ],
            [
                'id'    => 62,
                'title' => 'kb_item_quality_edit',
            ],
            [
                'id'    => 63,
                'title' => 'kb_item_quality_show',
            ],
            [
                'id'    => 64,
                'title' => 'kb_item_quality_delete',
            ],
            [
                'id'    => 65,
                'title' => 'kb_item_quality_access',
            ],
            [
                'id'    => 66,
                'title' => 'kb_item_status_create',
            ],
            [
                'id'    => 67,
                'title' => 'kb_item_status_edit',
            ],
            [
                'id'    => 68,
                'title' => 'kb_item_status_show',
            ],
            [
                'id'    => 69,
                'title' => 'kb_item_status_delete',
            ],
            [
                'id'    => 70,
                'title' => 'kb_item_status_access',
            ],
            [
                'id'    => 71,
                'title' => 'kb_item_category_create',
            ],
            [
                'id'    => 72,
                'title' => 'kb_item_category_edit',
            ],
            [
                'id'    => 73,
                'title' => 'kb_item_category_show',
            ],
            [
                'id'    => 74,
                'title' => 'kb_item_category_delete',
            ],
            [
                'id'    => 75,
                'title' => 'kb_item_category_access',
            ],
            [
                'id'    => 76,
                'title' => 'engagement_interaction_item_create',
            ],
            [
                'id'    => 77,
                'title' => 'engagement_interaction_item_edit',
            ],
            [
                'id'    => 78,
                'title' => 'engagement_interaction_item_show',
            ],
            [
                'id'    => 79,
                'title' => 'engagement_interaction_item_delete',
            ],
            [
                'id'    => 80,
                'title' => 'engagement_interaction_item_access',
            ],
            [
                'id'    => 81,
                'title' => 'engagement_interaction_type_create',
            ],
            [
                'id'    => 82,
                'title' => 'engagement_interaction_type_edit',
            ],
            [
                'id'    => 83,
                'title' => 'engagement_interaction_type_show',
            ],
            [
                'id'    => 84,
                'title' => 'engagement_interaction_type_delete',
            ],
            [
                'id'    => 85,
                'title' => 'engagement_interaction_type_access',
            ],
            [
                'id'    => 86,
                'title' => 'engagement_interaction_relation_create',
            ],
            [
                'id'    => 87,
                'title' => 'engagement_interaction_relation_edit',
            ],
            [
                'id'    => 88,
                'title' => 'engagement_interaction_relation_show',
            ],
            [
                'id'    => 89,
                'title' => 'engagement_interaction_relation_delete',
            ],
            [
                'id'    => 90,
                'title' => 'engagement_interaction_relation_access',
            ],
            [
                'id'    => 91,
                'title' => 'engagement_interaction_driver_create',
            ],
            [
                'id'    => 92,
                'title' => 'engagement_interaction_driver_edit',
            ],
            [
                'id'    => 93,
                'title' => 'engagement_interaction_driver_show',
            ],
            [
                'id'    => 94,
                'title' => 'engagement_interaction_driver_delete',
            ],
            [
                'id'    => 95,
                'title' => 'engagement_interaction_driver_access',
            ],
            [
                'id'    => 96,
                'title' => 'engagement_interaction_outcome_create',
            ],
            [
                'id'    => 97,
                'title' => 'engagement_interaction_outcome_edit',
            ],
            [
                'id'    => 98,
                'title' => 'engagement_interaction_outcome_show',
            ],
            [
                'id'    => 99,
                'title' => 'engagement_interaction_outcome_delete',
            ],
            [
                'id'    => 100,
                'title' => 'engagement_interaction_outcome_access',
            ],
            [
                'id'    => 101,
                'title' => 'support_item_create',
            ],
            [
                'id'    => 102,
                'title' => 'support_item_edit',
            ],
            [
                'id'    => 103,
                'title' => 'support_item_show',
            ],
            [
                'id'    => 104,
                'title' => 'support_item_delete',
            ],
            [
                'id'    => 105,
                'title' => 'support_item_access',
            ],
            [
                'id'    => 106,
                'title' => 'support_training_item_create',
            ],
            [
                'id'    => 107,
                'title' => 'support_training_item_edit',
            ],
            [
                'id'    => 108,
                'title' => 'support_training_item_show',
            ],
            [
                'id'    => 109,
                'title' => 'support_training_item_delete',
            ],
            [
                'id'    => 110,
                'title' => 'support_training_item_access',
            ],
            [
                'id'    => 111,
                'title' => 'support_feedback_item_create',
            ],
            [
                'id'    => 112,
                'title' => 'support_feedback_item_edit',
            ],
            [
                'id'    => 113,
                'title' => 'support_feedback_item_show',
            ],
            [
                'id'    => 114,
                'title' => 'support_feedback_item_delete',
            ],
            [
                'id'    => 115,
                'title' => 'support_feedback_item_access',
            ],
            [
                'id'    => 116,
                'title' => 'support_page_create',
            ],
            [
                'id'    => 117,
                'title' => 'support_page_edit',
            ],
            [
                'id'    => 118,
                'title' => 'support_page_show',
            ],
            [
                'id'    => 119,
                'title' => 'support_page_delete',
            ],
            [
                'id'    => 120,
                'title' => 'support_page_access',
            ],
            [
                'id'    => 121,
                'title' => 'record_enrollment_item_show',
            ],
            [
                'id'    => 122,
                'title' => 'record_enrollment_item_access',
            ],
            [
                'id'    => 123,
                'title' => 'record_program_item_show',
            ],
            [
                'id'    => 124,
                'title' => 'record_program_item_access',
            ],
            [
                'id'    => 125,
                'title' => 'record_student_item_show',
            ],
            [
                'id'    => 126,
                'title' => 'record_student_item_access',
            ],
            [
                'id'    => 127,
                'title' => 'engagement_email_item_create',
            ],
            [
                'id'    => 128,
                'title' => 'engagement_email_item_show',
            ],
            [
                'id'    => 129,
                'title' => 'engagement_email_item_delete',
            ],
            [
                'id'    => 130,
                'title' => 'engagement_email_item_access',
            ],
            [
                'id'    => 131,
                'title' => 'engagement_text_item_create',
            ],
            [
                'id'    => 132,
                'title' => 'engagement_text_item_show',
            ],
            [
                'id'    => 133,
                'title' => 'engagement_text_item_delete',
            ],
            [
                'id'    => 134,
                'title' => 'engagement_text_item_access',
            ],
            [
                'id'    => 135,
                'title' => 'engagement_student_file_create',
            ],
            [
                'id'    => 136,
                'title' => 'engagement_student_file_edit',
            ],
            [
                'id'    => 137,
                'title' => 'engagement_student_file_show',
            ],
            [
                'id'    => 138,
                'title' => 'engagement_student_file_delete',
            ],
            [
                'id'    => 139,
                'title' => 'engagement_student_file_access',
            ],
            [
                'id'    => 140,
                'title' => 'prospect_menu_access',
            ],
            [
                'id'    => 141,
                'title' => 'prospect_item_create',
            ],
            [
                'id'    => 142,
                'title' => 'prospect_item_edit',
            ],
            [
                'id'    => 143,
                'title' => 'prospect_item_show',
            ],
            [
                'id'    => 144,
                'title' => 'prospect_item_delete',
            ],
            [
                'id'    => 145,
                'title' => 'prospect_item_access',
            ],
            [
                'id'    => 146,
                'title' => 'case_update_item_create',
            ],
            [
                'id'    => 147,
                'title' => 'case_update_item_show',
            ],
            [
                'id'    => 148,
                'title' => 'case_update_item_access',
            ],
            [
                'id'    => 149,
                'title' => 'report_menu_access',
            ],
            [
                'id'    => 150,
                'title' => 'report_student_create',
            ],
            [
                'id'    => 151,
                'title' => 'report_student_edit',
            ],
            [
                'id'    => 152,
                'title' => 'report_student_show',
            ],
            [
                'id'    => 153,
                'title' => 'report_student_delete',
            ],
            [
                'id'    => 154,
                'title' => 'report_student_access',
            ],
            [
                'id'    => 155,
                'title' => 'report_prospect_create',
            ],
            [
                'id'    => 156,
                'title' => 'report_prospect_edit',
            ],
            [
                'id'    => 157,
                'title' => 'report_prospect_show',
            ],
            [
                'id'    => 158,
                'title' => 'report_prospect_delete',
            ],
            [
                'id'    => 159,
                'title' => 'report_prospect_access',
            ],
            [
                'id'    => 160,
                'title' => 'journey_menu_access',
            ],
            [
                'id'    => 161,
                'title' => 'journey_item_create',
            ],
            [
                'id'    => 162,
                'title' => 'journey_item_edit',
            ],
            [
                'id'    => 163,
                'title' => 'journey_item_show',
            ],
            [
                'id'    => 164,
                'title' => 'journey_item_delete',
            ],
            [
                'id'    => 165,
                'title' => 'journey_item_access',
            ],
            [
                'id'    => 166,
                'title' => 'journey_email_item_create',
            ],
            [
                'id'    => 167,
                'title' => 'journey_email_item_edit',
            ],
            [
                'id'    => 168,
                'title' => 'journey_email_item_show',
            ],
            [
                'id'    => 169,
                'title' => 'journey_email_item_delete',
            ],
            [
                'id'    => 170,
                'title' => 'journey_email_item_access',
            ],
            [
                'id'    => 171,
                'title' => 'journey_text_item_create',
            ],
            [
                'id'    => 172,
                'title' => 'journey_text_item_edit',
            ],
            [
                'id'    => 173,
                'title' => 'journey_text_item_show',
            ],
            [
                'id'    => 174,
                'title' => 'journey_text_item_delete',
            ],
            [
                'id'    => 175,
                'title' => 'journey_text_item_access',
            ],
            [
                'id'    => 176,
                'title' => 'journey_target_list_create',
            ],
            [
                'id'    => 177,
                'title' => 'journey_target_list_edit',
            ],
            [
                'id'    => 178,
                'title' => 'journey_target_list_show',
            ],
            [
                'id'    => 179,
                'title' => 'journey_target_list_delete',
            ],
            [
                'id'    => 180,
                'title' => 'journey_target_list_access',
            ],
            [
                'id'    => 181,
                'title' => 'prospect_status_create',
            ],
            [
                'id'    => 182,
                'title' => 'prospect_status_edit',
            ],
            [
                'id'    => 183,
                'title' => 'prospect_status_show',
            ],
            [
                'id'    => 184,
                'title' => 'prospect_status_delete',
            ],
            [
                'id'    => 185,
                'title' => 'prospect_status_access',
            ],
            [
                'id'    => 186,
                'title' => 'prospect_source_create',
            ],
            [
                'id'    => 187,
                'title' => 'prospect_source_edit',
            ],
            [
                'id'    => 188,
                'title' => 'prospect_source_show',
            ],
            [
                'id'    => 189,
                'title' => 'prospect_source_delete',
            ],
            [
                'id'    => 190,
                'title' => 'prospect_source_access',
            ],
        ];

        Permission::insert($permissions);
    }
}
