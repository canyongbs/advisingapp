<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * App\Models\SupportFeedbackItem
 *
 * @method static Builder|SupportFeedbackItem newModelQuery()
 * @method static Builder|SupportFeedbackItem newQuery()
 * @method static Builder|SupportFeedbackItem query()
 *
 * @mixin Eloquent
 */
class SupportFeedbackItem extends Model
{
    use HasFactory;

    // TODO: This was not originally present in the application, added it for now to not throw code analysis errors. We will need to figure out whether this is needed or not.
}
