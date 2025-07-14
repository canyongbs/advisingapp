<?php

namespace AdvisingApp\Prospect\Actions;

use AdvisingApp\Prospect\Models\Prospect;
use Illuminate\Support\Facades\DB;

class DeleteProspect
{
    public function execute(Prospect $prospect): void
    {
        DB::transaction(function () use ($prospect) {
            $prospect->emailAddresses()->delete();
            $prospect->phoneNumbers()->delete();
            $prospect->addresses()->delete();
            $prospect->delete();
        });
    }
}
