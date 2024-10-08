<?php

use Illuminate\Database\Migrations\Migration;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::createFunctionOrReplace(
            name: 'prevent_modification_of_system_protected_rows',
            parameters: [],
            return: 'TRIGGER',
            language: 'plpgsql',
            body: <<<SQL
                BEGIN
                    IF OLD.is_system_protected THEN
                        RAISE EXCEPTION 'Cannot modify system protected rows';
                    END IF;
                    RETURN NEW;
                END;
            SQL,
            options: [
                'security' => 'invoker',
                'volatility' => 'immutable',
                'parallel' => 'safe',
            ]
        );
    }

    public function down(): void
    {
        Schema::dropFunctionIfExists('prevent_modification_of_system_protected_rows');
    }
};
