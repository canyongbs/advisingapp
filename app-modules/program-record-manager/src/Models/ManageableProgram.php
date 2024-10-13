<?php

namespace AdvisingApp\ProgramRecordManager\Models;

use AdvisingApp\StudentDataModel\Models\Program;
use Illuminate\Database\Eloquent\SoftDeletes;

class ManageableProgram extends Program
{
    use SoftDeletes;

    protected $table = 'programs';

    protected $fillable = [
        'sisid',
        'otherid',
        'acad_career',
        'division',
        'acad_plan',
        'prog_status',
        'cum_gpa',
        'semester',
        'descr',
        'foi',
        'change_dt',
        'declare_dt',
    ];
}
