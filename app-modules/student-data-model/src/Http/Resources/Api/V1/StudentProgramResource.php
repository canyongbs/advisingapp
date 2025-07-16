<?php

namespace AdvisingApp\StudentDataModel\Http\Resources\Api\V1;

use AdvisingApp\StudentDataModel\Models\Program;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property Program $resource
 */
class StudentProgramResource extends JsonResource
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
            'acad_career' => $this->resource->acad_career,
            'division' => $this->resource->division,
            'acad_plan' => $this->resource->acad_plan,
            'prog_status' => $this->resource->prog_status,
            'cum_gpa' => (float) $this->resource->cum_gpa,
            'semester' => $this->resource->semester,
            'descr' => $this->resource->descr,
            'foi' => $this->resource->foi,
            'change_dt' => $this->resource->change_dt,
            'declare_dt' => $this->resource->declare_dt,
            'graduation_dt' => $this->resource->graduation_dt,
            'conferred_dt' => $this->resource->conferred_dt,
        ];
    }
}
