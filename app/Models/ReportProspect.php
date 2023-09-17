<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * App\Models\ReportProspect
 *
 * @method static Builder|ReportProspect newModelQuery()
 * @method static Builder|ReportProspect newQuery()
 * @method static Builder|ReportProspect query()
 *
 * @mixin Eloquent
 * @mixin IdeHelperReportProspect
 */
class ReportProspect extends Model
{
    use HasFactory;

    // TODO: This was not originally present in the application, added it for now to not throw code analysis errors. We will need to figure out whether this is needed or not.
}
