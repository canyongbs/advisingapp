<?php

use Illuminate\Support\Facades\DB;
use AdvisingApp\Segment\Models\Segment;
use Illuminate\Database\Migrations\Migration;
use AdvisingApp\Segment\Models\SegmentSubject;
use AdvisingApp\CaseloadManagement\Models\Caseload;

return new class () extends Migration {
    public function up(): void
    {
        Caseload::query()
            ->withTrashed()
            ->cursor()
            ->each(function (Caseload $caseload) {
                $segment = new Segment();
                $segment->name = $caseload->name;
                $segment->description = $caseload->description;
                $segment->filters = $caseload->filters;
                $segment->model = $caseload->model;
                $segment->type = $caseload->type;
                $segment->user_id = $caseload->user_id;
                $segment->created_at = $caseload->created_at;
                $segment->updated_at = $caseload->updated_at;
                $segment->deleted_at = $caseload->deleted_at;

                if ($caseload->subjects()->withTrashed()->exists()) {
                    foreach ($caseload->subjects()->withTrashed()->get() as $caseloadSubject) {
                        $segmentSubject = new SegmentSubject();
                        $segmentSubject->subject_id = $caseloadSubject->subject_id;
                        $segmentSubject->subject_type = $caseloadSubject->subject_type;
                        $segmentSubject->segment_id = $segment->id;
                        $segmentSubject->created_at = $caseloadSubject->created_at;
                        $segmentSubject->updated_at = $caseloadSubject->updated_at;
                        $segmentSubject->deleted_at = $caseloadSubject->deleted_at;
                    }
                }

                if ($caseload->campaigns()->withTrashed()->exists()) {
                    foreach ($caseload->campaigns()->withTrashed()->get() as $campaign) {
                        $campaign->segment()->associate($segment);
                    }
                }
            });
    }

    public function down(): void
    {
        DB::table('segments')->truncate();
        DB::table('segment_subjects')->truncate();
        DB::table('campaigns')->update(['segment_id' => null]);
    }
};
