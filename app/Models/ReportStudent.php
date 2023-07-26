<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * App\Models\ReportStudent
 *
 * @method static Builder|ReportStudent newModelQuery()
 * @method static Builder|ReportStudent newQuery()
 * @method static Builder|ReportStudent query()
 *
 * @mixin Eloquent
 */
class ReportStudent extends Model
{
    use HasFactory;

    // TODO: This was not originally present in the application, added it for now to not throw code analysis errors. We will need to figure out whether this is needed or not.
}
