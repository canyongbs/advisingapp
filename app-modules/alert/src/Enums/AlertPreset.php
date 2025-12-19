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
      of the licensor in the software. Any use of the licensor's trademarks is subject
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

namespace AdvisingApp\Alert\Enums;

use AdvisingApp\Alert\Presets\Handlers\AdultLearnerPresetHandler;
use AdvisingApp\Alert\Presets\Handlers\Contracts\AlertPresetHandler;
use AdvisingApp\Alert\Presets\Handlers\CourseWithdrawalPresetHandler;
use AdvisingApp\Alert\Presets\Handlers\CumulativeGpaBelowThresholdPresetHandler;
use AdvisingApp\Alert\Presets\Handlers\DorfGradePresetHandler;
use AdvisingApp\Alert\Presets\Handlers\FirstGenerationStudentPresetHandler;
use AdvisingApp\Alert\Presets\Handlers\MultipleCourseWithdrawalsPresetHandler;
use AdvisingApp\Alert\Presets\Handlers\MultipleDorfGradesPresetHandler;
use AdvisingApp\Alert\Presets\Handlers\NewStudentPresetHandler;
use AdvisingApp\Alert\Presets\Handlers\RepeatedCourseAttemptPresetHandler;
use AdvisingApp\Alert\Presets\Handlers\SemesterGpaBelowThresholdPresetHandler;
use Filament\Support\Contracts\HasLabel;

enum AlertPreset: string implements HasLabel
{
    case DorfGrade = 'd_or_f_grade';
    case MultipleDorfGrades = 'multiple_d_or_f_grades';
    case CourseWithdrawal = 'course_withdrawal';
    case MultipleCourseWithdrawals = 'multiple_course_withdrawals';
    case RepeatedCourseAttempt = 'repeated_course_attempt';
    case CumulativeGpaBelowThreshold = 'cumulative_gpa_below_threshold';
    case SemesterGpaBelowThreshold = 'semester_gpa_below_threshold';
    case FirstGenerationStudent = 'first_generation_student';
    case AdultLearner = 'adult_learner';
    case NewStudent = 'new_student';

    public function getLabel(): string
    {
        return match ($this) {
            self::DorfGrade => 'D or F Grade Posted',
            self::MultipleDorfGrades => 'Multiple D or F Grades Posted',
            self::CourseWithdrawal => 'Course Withdrawal',
            self::MultipleCourseWithdrawals => 'Multiple Course Withdrawals',
            self::RepeatedCourseAttempt => 'Repeated Course Attempt',
            self::CumulativeGpaBelowThreshold => 'Cumulative GPA Below Threshold',
            self::SemesterGpaBelowThreshold => 'Semester GPA Below Threshold',
            self::FirstGenerationStudent => 'First-Generation Student',
            self::AdultLearner => 'Adult Learner',
            self::NewStudent => 'New Student (First Semester at Institution)',
        };
    }

    /**
     * @return AlertPresetHandler
     */
    public function getHandler(): AlertPresetHandler
    {
        // /** @var AlertPresetHandler */
        return match ($this) {
            self::DorfGrade => new DorfGradePresetHandler(),
            self::MultipleDorfGrades => new MultipleDorfGradesPresetHandler(),
            self::CourseWithdrawal => new CourseWithdrawalPresetHandler(),
            self::MultipleCourseWithdrawals => new MultipleCourseWithdrawalsPresetHandler(),
            self::RepeatedCourseAttempt => new RepeatedCourseAttemptPresetHandler(),
            self::CumulativeGpaBelowThreshold => new CumulativeGpaBelowThresholdPresetHandler(),
            self::SemesterGpaBelowThreshold => new SemesterGpaBelowThresholdPresetHandler(),
            self::FirstGenerationStudent => new FirstGenerationStudentPresetHandler(),
            self::AdultLearner => new AdultLearnerPresetHandler(),
            self::NewStudent => new NewStudentPresetHandler(),
        };
    }
}
