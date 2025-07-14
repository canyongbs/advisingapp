<?php

namespace AdvisingApp\Prospect\Actions;

use AdvisingApp\Prospect\Models\ProspectEmailAddress;
use Illuminate\Support\Facades\DB;

class DeleteProspectEmailAddress
{
    public function execute(ProspectEmailAddress $prospectEmailAddress): void
    {
        DB::transaction(function () use ($prospectEmailAddress) {
            if ($prospectEmailAddress->prospect->primaryEmailAddress()->is($prospectEmailAddress)) {
                $prospectEmailAddress->prospect->primaryEmailAddress()->associate(
                    $prospectEmailAddress->prospect->emailAddresses()->whereKeyNot($prospectEmailAddress)->first(),
                );
                $prospectEmailAddress->prospect->save();
            }

            $prospectEmailAddress->delete();
        });
    }
}
