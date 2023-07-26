<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * App\Models\SupportItem
 *
 * @method static Builder|SupportItem newModelQuery()
 * @method static Builder|SupportItem newQuery()
 * @method static Builder|SupportItem query()
 *
 * @mixin Eloquent
 */
class SupportItem extends Model
{
    use HasFactory;

    // TODO: This was not originally present in the application, added it for now to not throw code analysis errors. We will need to figure out whether this is needed or not.
}
