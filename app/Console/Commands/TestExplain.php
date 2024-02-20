<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class TestExplain extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test-explain';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    public function handle()
    {
        Tenant::first()->makeCurrent();

        $query = "SELECT s.full_name, pa.cum_gpa AS gpa_a, pb.cum_gpa AS gpa_b, ((pa.cum_gpa - pb.cum_gpa)/pa.cum_gpa)*100 AS percent_change FROM students s JOIN programs pa ON s.sisid = pa.sisid AND pa.semester = 'Semester A' JOIN programs pb ON s.sisid = pb.sisid AND pb.semester = 'Semester B' WHERE ((pa.cum_gpa - pb.cum_gpa)/pa.cum_gpa)*100 > 20";

        $allowedTables = ['students', 'programs'];

        if ($this->queryAllowedTables($query, $allowedTables)) {
            $this->info('Query is allowed');
        } else {
            $this->error('Query is not allowed');
        }
    }

    public function queryAllowedTables(string $query, array $allowedTables): bool
    {
        $tables = $this->getTablesFromQuery($query);

        return collect($tables)->diff($allowedTables)->isEmpty();
    }

    public function getTablesFromQuery(string $query): Collection
    {
        $tables = [];

        $raw = DB::select("EXPLAIN {$query}");

        foreach ($raw as $row) {
            preg_match_all(
                '/(?<=Scan\son\s)(\w+)\s*?|Scan\susing\s\w+\son\s\K(\w+)\s*?/',
                $row->{'QUERY PLAN'},
                $matches,
                PREG_SET_ORDER
            );

            if (! empty($matches)) {
                $tables[] = $matches[0][0];
            }
        }

        return collect($tables)->unique()->values();
    }
}
