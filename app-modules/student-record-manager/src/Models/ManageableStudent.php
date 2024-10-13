<?php

namespace AdvisingApp\StudentRecordManager\Models;

use AdvisingApp\Alert\Models\Alert;
use AdvisingApp\StudentDataModel\Models\Student;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ManageableStudent extends Student
{
    use SoftDeletes;

    protected $table = 'students';

    protected $fillable = [
        'sisid',
        'otherid',
        'first',
        'last',
        'full_name',
        'preferred',
        'birthdate',
        'hsgrad',
        'email',
        'email_2',
        'mobile',
        'phone',
        'address',
        'address2',
        'address3',
        'city',
        'state',
        'postal',
        'sms_opt_out',
        'email_bounce',
        'dual',
        'ferpa',
        'dfw',
        'sap',
        'holds',
        'firstgen',
        'ethnicity',
        'lastlmslogin',
        'f_e_term',
        'mr_e_term',
    ];

    public function alerts(): MorphMany
    {
        return $this->morphMany(Alert::class, 'concern');
    }
}
