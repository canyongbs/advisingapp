<?php

declare(strict_types = 1);

use OpenSearch\Adapter\Indices\Mapping;
use OpenSearch\Adapter\Indices\Settings;
use OpenSearch\Migrations\Facades\Index;
use OpenSearch\Migrations\MigrationInterface;

final class CreateInteractionsIndex implements MigrationInterface
{
    public function up(): void
    {
        Index::createIfNotExists('interactions', function (Mapping $mapping, Settings $settings) {
            $mapping->keyword('id');
            $mapping->text('subject');
            $mapping->text('description');
            $mapping->keyword('user_id');
            $mapping->text('interactable_id');
            $mapping->text('interactable_type');
            $mapping->keyword('interaction_type_id');
            $mapping->text('type_name');
            $mapping->keyword('interaction_relation_id');
            $mapping->text('relation_name');
            $mapping->keyword('interaction_campaign_id');
            $mapping->text('campaign_name');
            $mapping->keyword('interaction_driver_id');
            $mapping->text('driver_name');
            $mapping->keyword('interaction_status_id');
            $mapping->text('status_name');
            $mapping->keyword('interaction_outcome_id');
            $mapping->text('outcome_name');
            $mapping->keyword('division_id');
            $mapping->text('division_name');
            $mapping->date('start_datetime', ['format' => 'yyyy-MM-dd HH:mm:ss']);
            $mapping->date('end_datetime', ['format' => 'yyyy-MM-dd HH:mm:ss']);
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
        Index::dropIfExists('interactions');
    }
}
