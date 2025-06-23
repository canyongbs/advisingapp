<?php

namespace AdvisingApp\CaseManagement\Models;

use AdvisingApp\CaseManagement\Database\Factories\CaseTypeAuditorFactory;
use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CaseTypeAuditor extends BaseModel
{
    /** @use HasFactory<CaseTypeAuditorFactory> */
    use HasFactory;
}
