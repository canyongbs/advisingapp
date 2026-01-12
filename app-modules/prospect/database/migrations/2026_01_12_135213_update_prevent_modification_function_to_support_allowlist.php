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
                DECLARE
                    old_data jsonb;
                    new_data jsonb;
                    i integer;
                BEGIN
                    -- Prevent deletion of system protected rows
                    IF TG_OP = 'DELETE' AND OLD.is_system_protected THEN
                        RAISE EXCEPTION 'Cannot delete system protected rows';
                    END IF;
                    
                    -- For updates on system protected rows, only allow specific columns to change
                    IF TG_OP = 'UPDATE' AND OLD.is_system_protected THEN
                        -- Convert rows to JSONB
                        old_data := to_jsonb(OLD.*);
                        new_data := to_jsonb(NEW.*);
                        
                        -- Remove allowed columns (passed as trigger arguments)
                        FOR i IN 0 .. TG_NARGS - 1 LOOP
                            old_data := old_data - TG_ARGV[i];
                            new_data := new_data - TG_ARGV[i];
                        END LOOP;
                        
                        -- Compare rows with allowed columns removed
                        IF old_data IS DISTINCT FROM new_data THEN
                            RAISE EXCEPTION 'Cannot modify system protected row columns';
                        END IF;
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
};
