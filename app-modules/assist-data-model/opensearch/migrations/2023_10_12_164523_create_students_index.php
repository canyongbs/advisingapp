<?php

declare(strict_types = 1);

use OpenSearch\Adapter\Indices\Mapping;
use OpenSearch\Adapter\Indices\Settings;
use OpenSearch\Migrations\Facades\Index;
use OpenSearch\Migrations\MigrationInterface;

final class CreateStudentsIndex implements MigrationInterface
{
    public function up(): void
    {
        Index::createIfNotExists('students', function (Mapping $mapping, Settings $settings) {
            $mapping->text('sisid');
            $mapping->text('otherid');
            $mapping->text('first');
            $mapping->text('last');
            $mapping->text('full_name', ['analyzer' => 'autocomplete']);
            $mapping->text('preferred');
            $mapping->text('email');
            $mapping->text('email_2');
            $mapping->text('mobile');
            $mapping->boolean('sms_opt_out');
            $mapping->boolean('email_bounce');
            $mapping->text('phone');
            $mapping->text('address');
            $mapping->text('address_2');
            $mapping->text('address_3');
            $mapping->text('city');
            $mapping->keyword('state');
            $mapping->keyword('postal');
            $mapping->date('birthdate', ['format' => 'date']);
            $mapping->integer('hsgrad');
            $mapping->text('dual');
            $mapping->text('ferpa');
            $mapping->text('dfw');
            $mapping->text('sap');
            $mapping->text('holds');
            $mapping->boolean('firstgen');
            $mapping->text('ethnicity');
            $mapping->date('lastlmslogin', ['format' => 'date_time']);
            $mapping->text('f_e_term');
            $mapping->text('mr_e_term');

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
        Index::dropIfExists('students');
    }
}
