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

namespace AdvisingApp\StudentDataModel\Http\Resources\Api\V1;

use AdvisingApp\StudentDataModel\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property Enrollment $resource
 */
class StudentEnrollmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'sisid' => $this->resource->sisid,
            'division' => $this->resource->division,
            'class_nbr' => $this->resource->class_nbr,
            'crse_grade_off' => $this->resource->crse_grade_off,
            'unt_taken' => $this->resource->unt_taken,
            'unt_earned' => $this->resource->unt_earned,
            'last_upd_dt_stmp' => $this->resource->last_upd_dt_stmp,
            'section' => $this->resource->section,
            'name' => $this->resource->name,
            'department' => $this->resource->department,
            'faculty_name' => $this->resource->faculty_name,
            'faculty_email' => $this->resource->faculty_email,
            'semester_code' => $this->resource->semester_code,
            'semester_name' => $this->resource->semester_name,
            'start_date' => $this->resource->start_date,
            'end_date' => $this->resource->end_date,
        ];
    }
}
