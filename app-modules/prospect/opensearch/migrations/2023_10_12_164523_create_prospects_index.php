<?php

declare(strict_types = 1);

use OpenSearch\Adapter\Indices\Mapping;
use OpenSearch\Adapter\Indices\Settings;
use OpenSearch\Migrations\Facades\Index;
use OpenSearch\Migrations\MigrationInterface;

final class CreateProspectsIndex implements MigrationInterface
{
    /**
     * Run the migration.
     */
    public function up(): void
    {
        Index::create('prospects', function (Mapping $mapping, Settings $settings) {
            $mapping->keyword('id');
            $mapping->keyword('status_id');
            $mapping->keyword('source_id');
            $mapping->text('first_name');
            $mapping->text('last_name');
            $mapping->text('full_name', ['analyzer' => 'autocomplete']);
            $mapping->text('preferred');
            $mapping->text('description');
            $mapping->text('email');
            $mapping->text('email_2');
            $mapping->text('mobile');
            $mapping->boolean('sms_opt_out');
            $mapping->boolean('email_bounce');
            $mapping->text('phone');
            $mapping->text('address');
            $mapping->text('address_2');
            $mapping->date('birthdate', ['format' => 'date']);
            $mapping->integer('hsgrad');
            $mapping->text('assigned_to_id');
            $mapping->text('created_by_id');
            $mapping->date('created_at', ['format' => 'yyyy-MM-dd HH:mm:ss']);
            $mapping->date('updated_at', ['format' => 'yyyy-MM-dd HH:mm:ss']);

            $settings->analysis([
                'filter' => [
                    'edge_ngram_filter' => [
                        'type' => 'edge_ngram',
                        'min_gram' => 1,
                        'max_gram' => 20,
                    ],
                ],
                'analyzer' => [
                    'autocomplete' => [
                        'type' => 'custom',
                        'tokenizer' => 'standard',
                        'filter' => [
                            'lowercase',
                            'edge_ngram_filter',
                        ],
                    ],
                ],
            ]);
        });
    }

    public function down(): void
    {
        Index::dropIfExists('prospects');
    }
}
