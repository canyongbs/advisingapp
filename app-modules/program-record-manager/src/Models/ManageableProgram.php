<?php

namespace AdvisingApp\ProgramRecordManager\Models;

use AdvisingApp\StudentDataModel\Models\Program;

class ManageableProgram extends Program
{
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
