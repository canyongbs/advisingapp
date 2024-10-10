<?php

namespace AdvisingApp\StudentRecordManager\Models;

use AdvisingApp\StudentDataModel\Models\Student;

class ManageableStudent extends Student
{
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
}
