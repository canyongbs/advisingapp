<?php

namespace Assist\CaseloadManagement\Console\Commands;

use Illuminate\Console\Command;
use Assist\CaseloadManagement\Models\Caseload;
use Assist\CaseloadManagement\Actions\TranslateCaseloadFilter;

class TranslateCaseload extends Command
{
    protected $signature = 'caseloads:translate';

    protected $description = 'Translate caseload filters.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Caseload::all()
            ->each(function (Caseload $caseload) {
                ray(resolve(TranslateCaseloadFilter::class)->handle($caseload));
            });
    }
}
