<?php

declare(strict_types = 1);

use OpenSearch\Adapter\Indices\Mapping;
use OpenSearch\Adapter\Indices\Settings;
use OpenSearch\Migrations\Facades\Index;
use OpenSearch\Migrations\MigrationInterface;

final class CreateServiceRequestsIndex implements MigrationInterface
{
    public function up(): void
    {
        Index::createIfNotExists('service_requests', function (Mapping $mapping, Settings $settings) {
            $mapping->keyword('id');
            $mapping->text('service_request_number');
            $mapping->text('respondent_type');
            $mapping->text('respondent_id');
            $mapping->text('respondent_name');
            $mapping->text('close_details');
            $mapping->text('res_details');
            $mapping->keyword('division_id');
            $mapping->keyword('status_id');
            $mapping->keyword('type_id');
            $mapping->keyword('priority_id');
            $mapping->keyword('assigned_to_id');
            $mapping->keyword('created_by_id');
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
        Index::dropIfExists('service_requests');
    }
}
