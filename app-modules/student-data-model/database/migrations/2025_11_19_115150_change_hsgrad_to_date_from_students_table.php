<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class () extends Migration {
    public function up(): void
    {
        DB::statement('UPDATE students SET hsgrad = NULL');

        DB::statement('
            ALTER TABLE students
            ALTER COLUMN hsgrad TYPE date
            USING (NULL)
        ');

        DB::statement('
            ALTER TABLE students
            ALTER COLUMN hsgrad DROP NOT NULL
        ');
    }

    public function down(): void
    {
        DB::statement('
            ALTER TABLE students
            ALTER COLUMN hsgrad TYPE integer
        ');
    }
};
