<?php

declare(strict_types = 1);

use OpenSearch\Adapter\Indices\Mapping;
use OpenSearch\Adapter\Indices\Settings;
use OpenSearch\Migrations\Facades\Index;
use OpenSearch\Migrations\MigrationInterface;

final class CreateKnowledgeBaseItemsIndex implements MigrationInterface
{
    public function up(): void
    {
        Index::createIfNotExists('knowledge_base_items', function (Mapping $mapping, Settings $settings) {
            $mapping->keyword('id');
            $mapping->text('question');
            $mapping->boolean('public');
            $mapping->text('solution');
            $mapping->text('notes');
            $mapping->keyword('quality_id');
            $mapping->text('quality_name');
            $mapping->keyword('status_id');
            $mapping->text('status_name');
            $mapping->keyword('category_id');
            $mapping->text('category_name');
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
        Index::dropIfExists('knowledge_base_items');
    }
}
