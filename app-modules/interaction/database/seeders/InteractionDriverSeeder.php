<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS LLC. All rights reserved.

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

namespace AdvisingApp\Interaction\Database\Seeders;

use AdvisingApp\Interaction\Models\InteractionDriver;
use App\Features\InteractableTypeFeature;
use Illuminate\Database\Seeder;

class InteractionDriverSeeder extends Seeder
{
    public function run(): void
    {
        InteractableTypeFeature::active() ? 
          InteractionDriver::factory()
              ->createMany(
                  [
                      ['name' => 'Add Class Self Service', 'interactable_type' => 'student'],
                      ['name' => 'Admission Application', 'interactable_type' => 'student'],
                      ['name' => 'Advising', 'interactable_type' => 'student'],
                      ['name' => 'Application WS', 'interactable_type' => 'student'],
                      ['name' => 'Attending Other System College', 'interactable_type' => 'student'],
                      ['name' => 'Bachelor Degree', 'interactable_type' => 'student'],
                      ['name' => 'Book Advance', 'interactable_type' => 'student'],
                      ['name' => 'Campus Event', 'interactable_type' => 'student'],
                      ['name' => 'Campus Tour', 'interactable_type' => 'student'],
                      ['name' => 'College Fair Event', 'interactable_type' => 'student'],
                      ['name' => 'Consortium Agreement', 'interactable_type' => 'student'],
                      ['name' => 'Course Applicability', 'interactable_type' => 'student'],
                      ['name' => 'Course Selection', 'interactable_type' => 'student'],
                      ['name' => 'Child Care Grant', 'interactable_type' => 'student'],
                      ['name' => 'Disbursement', 'interactable_type' => 'student'],
                      ['name' => 'Drop or Withdrawal', 'interactable_type' => 'student'],
                      ['name' => 'MFA Authentication Support', 'interactable_type' => 'student'],
                      ['name' => 'Early College', 'interactable_type' => 'student'],
                      ['name' => 'Enrolled Student', 'interactable_type' => 'student'],
                      ['name' => 'Enrollment Cancellation', 'interactable_type' => 'student'],
                      ['name' => 'Enrollment Steps', 'interactable_type' => 'student'],
                      ['name' => 'Enrollment Verification', 'interactable_type' => 'student'],
                      ['name' => 'ESL', 'interactable_type' => 'student'],
                      ['name' => 'Financial Aid Status', 'interactable_type' => 'student'],
                      ['name' => 'Financial Aid Verification', 'interactable_type' => 'student'],
                      ['name' => 'FAFSA', 'interactable_type' => 'student'],
                      ['name' => 'FAFSA Completion Event', 'interactable_type' => 'student'],
                      ['name' => 'Fast Track Certificates', 'interactable_type' => 'student'],
                      ['name' => 'Free College', 'interactable_type' => 'student'],
                      ['name' => 'GED', 'interactable_type' => 'student'],
                      ['name' => 'Graduation', 'interactable_type' => 'student'],
                      ['name' => 'HEERF', 'interactable_type' => 'student'],
                      ['name' => 'HS Visit', 'interactable_type' => 'student'],
                      ['name' => 'HSI EXCELlence', 'interactable_type' => 'student'],
                      ['name' => 'ID Authentication', 'interactable_type' => 'student'],
                      ['name' => 'Incoming Transcripts', 'interactable_type' => 'student'],
                      ['name' => 'Information Session 1 on 1', 'interactable_type' => 'student'],
                      ['name' => 'Loan', 'interactable_type' => 'student'],
                      ['name' => 'Loan Entrance Counseling', 'interactable_type' => 'student'],
                      ['name' => 'Master Promissory Note', 'interactable_type' => 'student'],
                      ['name' => 'Military Benefits', 'interactable_type' => 'student'],
                      ['name' => 'MOV Activity', 'interactable_type' => 'student'],
                      ['name' => 'New Student Orientation', 'interactable_type' => 'student'],
                      ['name' => 'No Longer Interested', 'interactable_type' => 'student'],
                      ['name' => 'On Campus Outreach Recruitment Event', 'interactable_type' => 'student'],
                      ['name' => 'Other or General Information', 'interactable_type' => 'student'],
                      ['name' => 'Outgoing Transcripts', 'interactable_type' => 'student'],
                      ['name' => 'Outreach Outcomes', 'interactable_type' => 'student'],
                      ['name' => 'Password Reset', 'interactable_type' => 'student'],
                      ['name' => 'Pell', 'interactable_type' => 'student'],
                      ['name' => 'Placement', 'interactable_type' => 'student'],
                      ['name' => 'Prerequisites', 'interactable_type' => 'student'],
                      ['name' => 'Private Loans', 'interactable_type' => 'student'],
                      ['name' => 'R2T4', 'interactable_type' => 'student'],
                      ['name' => 'Reengagement', 'interactable_type' => 'student'],
                      ['name' => 'Refund', 'interactable_type' => 'student'],
                      ['name' => 'Residency', 'interactable_type' => 'student'],
                      ['name' => 'SAP and Maximum Time Frame', 'interactable_type' => 'student'],
                      ['name' => 'SBS or Cashier', 'interactable_type' => 'student'],
                      ['name' => 'Scholarships', 'interactable_type' => 'student'],
                      ['name' => 'SEM Outreach', 'interactable_type' => 'student'],
                      ['name' => 'Special Circumstance', 'interactable_type' => 'student'],
                      ['name' => 'Technical Support', 'interactable_type' => 'student'],
                      ['name' => 'Term Activation', 'interactable_type' => 'student'],
                      ['name' => 'To Do List/Checklist', 'interactable_type' => 'student'],
                      ['name' => 'Transfer to College', 'interactable_type' => 'student'],
                      ['name' => 'Tuition or Fees', 'interactable_type' => 'student'],
                      ['name' => 'Basic Needs', 'interactable_type' => 'student'],
                      ['name' => 'Add Class Self Service', 'interactable_type' => 'prospect'],
                      ['name' => 'Admission Application', 'interactable_type' => 'prospect'],
                      ['name' => 'Advising', 'interactable_type' => 'prospect'],
                      ['name' => 'Application WS', 'interactable_type' => 'prospect'],
                      ['name' => 'Attending Other System College', 'interactable_type' => 'prospect'],
                      ['name' => 'Bachelor Degree', 'interactable_type' => 'prospect'],
                      ['name' => 'Book Advance', 'interactable_type' => 'prospect'],
                      ['name' => 'Campus Event', 'interactable_type' => 'prospect'],
                      ['name' => 'Campus Tour', 'interactable_type' => 'prospect'],
                      ['name' => 'College Fair Event', 'interactable_type' => 'prospect'],
                      ['name' => 'Consortium Agreement', 'interactable_type' => 'prospect'],
                      ['name' => 'Course Applicability', 'interactable_type' => 'prospect'],
                      ['name' => 'Course Selection', 'interactable_type' => 'prospect'],
                      ['name' => 'Child Care Grant', 'interactable_type' => 'prospect'],
                      ['name' => 'Disbursement', 'interactable_type' => 'prospect'],
                      ['name' => 'Drop or Withdrawal', 'interactable_type' => 'prospect'],
                      ['name' => 'MFA Authentication Support', 'interactable_type' => 'prospect'],
                      ['name' => 'Early College', 'interactable_type' => 'prospect'],
                      ['name' => 'Enrolled prospect', 'interactable_type' => 'prospect'],
                      ['name' => 'Enrollment Cancellation', 'interactable_type' => 'prospect'],
                      ['name' => 'Enrollment Steps', 'interactable_type' => 'prospect'],
                      ['name' => 'Enrollment Verification', 'interactable_type' => 'prospect'],
                      ['name' => 'ESL', 'interactable_type' => 'prospect'],
                      ['name' => 'Financial Aid Status', 'interactable_type' => 'prospect'],
                      ['name' => 'Financial Aid Verification', 'interactable_type' => 'prospect'],
                      ['name' => 'FAFSA', 'interactable_type' => 'prospect'],
                      ['name' => 'FAFSA Completion Event', 'interactable_type' => 'prospect'],
                      ['name' => 'Fast Track Certificates', 'interactable_type' => 'prospect'],
                      ['name' => 'Free College', 'interactable_type' => 'prospect'],
                      ['name' => 'GED', 'interactable_type' => 'prospect'],
                      ['name' => 'Graduation', 'interactable_type' => 'prospect'],
                      ['name' => 'HEERF', 'interactable_type' => 'prospect'],
                      ['name' => 'HS Visit', 'interactable_type' => 'prospect'],
                      ['name' => 'HSI EXCELlence', 'interactable_type' => 'prospect'],
                      ['name' => 'ID Authentication', 'interactable_type' => 'prospect'],
                      ['name' => 'Incoming Transcripts', 'interactable_type' => 'prospect'],
                      ['name' => 'Information Session 1 on 1', 'interactable_type' => 'prospect'],
                      ['name' => 'Loan', 'interactable_type' => 'prospect'],
                      ['name' => 'Loan Entrance Counseling', 'interactable_type' => 'prospect'],
                      ['name' => 'Master Promissory Note', 'interactable_type' => 'prospect'],
                      ['name' => 'Military Benefits', 'interactable_type' => 'prospect'],
                      ['name' => 'MOV Activity', 'interactable_type' => 'prospect'],
                      ['name' => 'New prospect Orientation', 'interactable_type' => 'prospect'],
                      ['name' => 'No Longer Interested', 'interactable_type' => 'prospect'],
                      ['name' => 'On Campus Outreach Recruitment Event', 'interactable_type' => 'prospect'],
                      ['name' => 'Other or General Information', 'interactable_type' => 'prospect'],
                      ['name' => 'Outgoing Transcripts', 'interactable_type' => 'prospect'],
                      ['name' => 'Outreach Outcomes', 'interactable_type' => 'prospect'],
                      ['name' => 'Password Reset', 'interactable_type' => 'prospect'],
                      ['name' => 'Pell', 'interactable_type' => 'prospect'],
                      ['name' => 'Placement', 'interactable_type' => 'prospect'],
                      ['name' => 'Prerequisites', 'interactable_type' => 'prospect'],
                      ['name' => 'Private Loans', 'interactable_type' => 'prospect'],
                      ['name' => 'R2T4', 'interactable_type' => 'prospect'],
                      ['name' => 'Reengagement', 'interactable_type' => 'prospect'],
                      ['name' => 'Refund', 'interactable_type' => 'prospect'],
                      ['name' => 'Residency', 'interactable_type' => 'prospect'],
                      ['name' => 'SAP and Maximum Time Frame', 'interactable_type' => 'prospect'],
                      ['name' => 'SBS or Cashier', 'interactable_type' => 'prospect'],
                      ['name' => 'Scholarships', 'interactable_type' => 'prospect'],
                      ['name' => 'SEM Outreach', 'interactable_type' => 'prospect'],
                      ['name' => 'Special Circumstance', 'interactable_type' => 'prospect'],
                      ['name' => 'Technical Support', 'interactable_type' => 'prospect'],
                      ['name' => 'Term Activation', 'interactable_type' => 'prospect'],
                      ['name' => 'To Do List/Checklist', 'interactable_type' => 'prospect'],
                      ['name' => 'Transfer to College', 'interactable_type' => 'prospect'],
                      ['name' => 'Tuition or Fees', 'interactable_type' => 'prospect'],
                      ['name' => 'Basic Needs', 'interactable_type' => 'prospect'],
                  ]
              ) :
          InteractionDriver::factory()
              ->createMany(
                  [
                      ['name' => 'Add Class Self Service'],
                      ['name' => 'Admission Application'],
                      ['name' => 'Advising'],
                      ['name' => 'Application WS'],
                      ['name' => 'Attending Other System College'],
                      ['name' => 'Bachelor Degree'],
                      ['name' => 'Book Advance'],
                      ['name' => 'Campus Event'],
                      ['name' => 'Campus Tour'],
                      ['name' => 'College Fair Event'],
                      ['name' => 'Consortium Agreement'],
                      ['name' => 'Course Applicability'],
                      ['name' => 'Course Selection'],
                      ['name' => 'Child Care Grant'],
                      ['name' => 'Disbursement'],
                      ['name' => 'Drop or Withdrawal'],
                      ['name' => 'MFA Authentication Support'],
                      ['name' => 'Early College'],
                      ['name' => 'Enrolled Student'],
                      ['name' => 'Enrollment Cancellation'],
                      ['name' => 'Enrollment Steps'],
                      ['name' => 'Enrollment Verification'],
                      ['name' => 'ESL'],
                      ['name' => 'Financial Aid Status'],
                      ['name' => 'Financial Aid Verification'],
                      ['name' => 'FAFSA'],
                      ['name' => 'FAFSA Completion Event'],
                      ['name' => 'Fast Track Certificates'],
                      ['name' => 'Free College'],
                      ['name' => 'GED'],
                      ['name' => 'Graduation'],
                      ['name' => 'HEERF'],
                      ['name' => 'HS Visit'],
                      ['name' => 'HSI EXCELlence'],
                      ['name' => 'ID Authentication'],
                      ['name' => 'Incoming Transcripts'],
                      ['name' => 'Information Session 1 on 1'],
                      ['name' => 'Loan'],
                      ['name' => 'Loan Entrance Counseling'],
                      ['name' => 'Master Promissory Note'],
                      ['name' => 'Military Benefits'],
                      ['name' => 'MOV Activity'],
                      ['name' => 'New Student Orientation'],
                      ['name' => 'No Longer Interested'],
                      ['name' => 'On Campus Outreach Recruitment Event'],
                      ['name' => 'Other or General Information'],
                      ['name' => 'Outgoing Transcripts'],
                      ['name' => 'Outreach Outcomes'],
                      ['name' => 'Password Reset'],
                      ['name' => 'Pell'],
                      ['name' => 'Placement'],
                      ['name' => 'Prerequisites'],
                      ['name' => 'Private Loans'],
                      ['name' => 'R2T4'],
                      ['name' => 'Reengagement'],
                      ['name' => 'Refund'],
                      ['name' => 'Residency'],
                      ['name' => 'SAP and Maximum Time Frame'],
                      ['name' => 'SBS or Cashier'],
                      ['name' => 'Scholarships'],
                      ['name' => 'SEM Outreach'],
                      ['name' => 'Special Circumstance'],
                      ['name' => 'Technical Support'],
                      ['name' => 'Term Activation'],
                      ['name' => 'To Do List/Checklist'],
                      ['name' => 'Transfer to College'],
                      ['name' => 'Tuition or Fees'],
                      ['name' => 'Basic Needs'],
                  ]
              );
    }
}
