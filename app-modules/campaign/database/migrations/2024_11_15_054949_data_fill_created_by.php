<?php

use AdvisingApp\Campaign\Models\Campaign;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        Campaign::with('user')->each(function ($campaign) {
            $campaign->created_by_id = $campaign->user?->getKey();
            $campaign->created_by_type = 'user';
            $campaign->save();
        });
    }
};
