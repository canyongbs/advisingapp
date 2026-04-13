<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS LLC. All rights reserved.

    Advising App™ is licensed under the Elastic License 2.0. For more details,
    see https://github.com/canyongbs/advisingapp/blob/main/LICENSE.

    Notice:

    - You may not provide the software to third parties as a hosted or managed
      service, where the service provides users with access to any substantial set of
      the features or functionality of the software.
    - You may not move, change, disable, or circumvent the license key functionality
      in the software, and you may not remove or obscure any functionality in the
      software that is protected by the license key.
    - You may not alter, remove, or obscure any licensing, copyright, or other notices
      of the licensor in the software. Any use of the licensor’s trademarks is subject
      to applicable law.
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Advising App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

use AdvisingApp\ResourceHub\Models\ResourceHubArticle;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

// Add tests for migration files here

// Example migration test, leave commented out for future use as a template/example
//describe('2025_01_01_165527_tmp_data_do_a_thing', function () {
//    it('properly changed the data', function () {
//        isolatedMigration(
//            '2025_01_01_165527_tmp_data_do_a_thing',
//            function () {
//                // Setup data before migration
//
//                // Run the migration
//                $migrate = Artisan::call('migrate', ['--path' => 'app/database/migrations/2025_01_01_165527_tmp_data_do_a_thing.php']);
//                // Confirm migration ran successfully
//                expect($migrate)->toBe(Command::SUCCESS);
//
//                // Add any assertions to verify the migration's effects
//            }
//        );
//    });
//});

test('2026_04_13_000000_tmp_data_migrate_resource_hub_articles_to_rich_editor_format transforms grids', function () {
    isolatedMigration(
        '2026_04_13_000000_tmp_data_migrate_resource_hub_articles_to_rich_editor_format',
        function () {
            $item = ResourceHubArticle::factory()->createQuietly([
                'article_details' => [
                    'type' => 'doc',
                    'content' => [
                        [
                            'type' => 'grid',
                            'attrs' => ['type' => 'fixed', 'cols' => '3'],
                            'content' => [
                                ['type' => 'gridColumn', 'attrs' => [], 'content' => [['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => 'Col 1']]]]],
                                ['type' => 'gridColumn', 'attrs' => [], 'content' => [['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => 'Col 2']]]]],
                                ['type' => 'gridColumn', 'attrs' => [], 'content' => [['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => 'Col 3']]]]],
                            ],
                        ],
                    ],
                ],
            ]);

            $migrate = Artisan::call('migrate', ['--path' => 'app-modules/resource-hub/database/migrations/2026_04_13_000000_tmp_data_migrate_resource_hub_articles_to_rich_editor_format.php']);

            expect($migrate)->toBe(Command::SUCCESS);

            $content = json_decode((string) DB::table('resource_hub_articles')->where('id', $item->id)->value('article_details'), associative: true); /** @phpstan-ignore-line */

            /** @phpstan-ignore-next-line */
            expect($content['content'][0]['attrs']['data-cols'])->toBe('3');
            /** @phpstan-ignore-next-line */
            expect($content['content'][0]['attrs']['data-from-breakpoint'])->toBe('sm');
            /** @phpstan-ignore-next-line */
            expect($content['content'][0]['content'][0]['attrs']['data-col-span'])->toBe('1');
        }
    );
});

test('2026_04_13_000000_tmp_data_migrate_resource_hub_articles_to_rich_editor_format transforms youtube', function () {
    isolatedMigration(
        '2026_04_13_000000_tmp_data_migrate_resource_hub_articles_to_rich_editor_format',
        function () {
            $item = ResourceHubArticle::factory()->createQuietly([
                'article_details' => [
                    'type' => 'doc',
                    'content' => [
                        [
                            'type' => 'youtube',
                            'attrs' => ['src' => 'https://www.youtube.com/embed/dQw4w9WgXcQ', 'width' => 640, 'height' => 480],
                        ],
                    ],
                ],
            ]);

            $migrate = Artisan::call('migrate', ['--path' => 'app-modules/resource-hub/database/migrations/2026_04_13_000000_tmp_data_migrate_resource_hub_articles_to_rich_editor_format.php']);

            expect($migrate)->toBe(Command::SUCCESS);

            $content = json_decode((string) DB::table('resource_hub_articles')->where('id', $item->id)->value('article_details'), associative: true); /** @phpstan-ignore-line */

            /** @phpstan-ignore-next-line */
            expect($content['content'][0]['type'])->toBe('videoEmbed');
            /** @phpstan-ignore-next-line */
            expect($content['content'][0]['attrs']['src'])->toBe('https://www.youtube.com/embed/dQw4w9WgXcQ');
            /** @phpstan-ignore-next-line */
            expect($content['content'][0]['attrs']['type'])->toBe('youtube');
            /** @phpstan-ignore-next-line */
            expect($content['content'][0]['attrs']['width'])->toBeNull();
            /** @phpstan-ignore-next-line */
            expect($content['content'][0]['attrs']['height'])->toBeNull();
        }
    );
});

test('2026_04_13_000000_tmp_data_migrate_resource_hub_articles_to_rich_editor_format transforms vimeo', function () {
    isolatedMigration(
        '2026_04_13_000000_tmp_data_migrate_resource_hub_articles_to_rich_editor_format',
        function () {
            $item = ResourceHubArticle::factory()->createQuietly([
                'article_details' => [
                    'type' => 'doc',
                    'content' => [
                        [
                            'type' => 'vimeo',
                            'attrs' => ['src' => 'https://player.vimeo.com/video/123456789', 'width' => 640, 'height' => 480],
                        ],
                    ],
                ],
            ]);

            $migrate = Artisan::call('migrate', ['--path' => 'app-modules/resource-hub/database/migrations/2026_04_13_000000_tmp_data_migrate_resource_hub_articles_to_rich_editor_format.php']);

            expect($migrate)->toBe(Command::SUCCESS);

            $content = json_decode((string) DB::table('resource_hub_articles')->where('id', $item->id)->value('article_details'), associative: true); /** @phpstan-ignore-line */

            /** @phpstan-ignore-next-line */
            expect($content['content'][0]['type'])->toBe('videoEmbed');
            /** @phpstan-ignore-next-line */
            expect($content['content'][0]['attrs']['type'])->toBe('vimeo');
        }
    );
});

test('2026_04_13_000000_tmp_data_migrate_resource_hub_articles_to_rich_editor_format removes hurdles and preserves children', function () {
    isolatedMigration(
        '2026_04_13_000000_tmp_data_migrate_resource_hub_articles_to_rich_editor_format',
        function () {
            $item = ResourceHubArticle::factory()->createQuietly([
                'article_details' => [
                    'type' => 'doc',
                    'content' => [
                        [
                            'type' => 'hurdle',
                            'attrs' => ['color' => 'gray_light'],
                            'content' => [
                                ['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => 'Inside hurdle']]],
                            ],
                        ],
                        [
                            'type' => 'paragraph',
                            'content' => [['type' => 'text', 'text' => 'After hurdle']],
                        ],
                    ],
                ],
            ]);

            $migrate = Artisan::call('migrate', ['--path' => 'app-modules/resource-hub/database/migrations/2026_04_13_000000_tmp_data_migrate_resource_hub_articles_to_rich_editor_format.php']);

            expect($migrate)->toBe(Command::SUCCESS);

            $content = json_decode((string) DB::table('resource_hub_articles')->where('id', $item->id)->value('article_details'), associative: true); /** @phpstan-ignore-line */

            // Hurdle should be removed but its child paragraph preserved
            /** @phpstan-ignore-next-line */
            expect($content['content'][0]['type'])->toBe('paragraph');
            /** @phpstan-ignore-next-line */
            expect($content['content'][0]['content'][0]['text'])->toBe('Inside hurdle');
            /** @phpstan-ignore-next-line */
            expect($content['content'][1]['type'])->toBe('paragraph');
            /** @phpstan-ignore-next-line */
            expect($content['content'][1]['content'][0]['text'])->toBe('After hurdle');
        }
    );
});

test('2026_04_13_000000_tmp_data_migrate_resource_hub_articles_to_rich_editor_format transforms oversized images', function () {
    isolatedMigration(
        '2026_04_13_000000_tmp_data_migrate_resource_hub_articles_to_rich_editor_format',
        function () {
            $item = ResourceHubArticle::factory()->createQuietly([
                'article_details' => [
                    'type' => 'doc',
                    'content' => [
                        [
                            'type' => 'paragraph',
                            'content' => [
                                [
                                    'type' => 'image',
                                    'attrs' => [
                                        'id' => 'test-uuid',
                                        'src' => null,
                                        'width' => 800,
                                        'height' => 600,
                                    ],
                                ],
                            ],
                        ],
                        [
                            'type' => 'paragraph',
                            'content' => [
                                [
                                    'type' => 'image',
                                    'attrs' => [
                                        'id' => 'small-uuid',
                                        'src' => null,
                                        'width' => 300,
                                        'height' => 200,
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ]);

            $migrate = Artisan::call('migrate', ['--path' => 'app-modules/resource-hub/database/migrations/2026_04_13_000000_tmp_data_migrate_resource_hub_articles_to_rich_editor_format.php']);

            expect($migrate)->toBe(Command::SUCCESS);

            $content = json_decode((string) DB::table('resource_hub_articles')->where('id', $item->id)->value('article_details'), associative: true); /** @phpstan-ignore-line */

            /** @phpstan-ignore-next-line */
            expect($content['content'][0]['content'][0]['attrs']['width'])->toBeNull();
            /** @phpstan-ignore-next-line */
            expect($content['content'][0]['content'][0]['attrs']['height'])->toBeNull();

            /** @phpstan-ignore-next-line */
            expect($content['content'][1]['content'][0]['attrs']['width'])->toBe(300);
            /** @phpstan-ignore-next-line */
            expect($content['content'][1]['content'][0]['attrs']['height'])->toBe(200);
        }
    );
});

test('2026_04_13_000000_tmp_data_migrate_resource_hub_articles_to_rich_editor_format transforms gridBuilder', function () {
    isolatedMigration(
        '2026_04_13_000000_tmp_data_migrate_resource_hub_articles_to_rich_editor_format',
        function () {
            $item = ResourceHubArticle::factory()->createQuietly([
                'article_details' => [
                    'type' => 'doc',
                    'content' => [
                        [
                            'type' => 'gridBuilder',
                            'attrs' => ['data-type' => 'responsive', 'data-cols' => 5, 'data-stack-at' => 'sm'],
                            'content' => [
                                ['type' => 'gridBuilderColumn', 'attrs' => ['data-col-span' => null], 'content' => [['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => '1']]]]],
                                ['type' => 'gridBuilderColumn', 'attrs' => ['data-col-span' => null], 'content' => [['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => '2']]]]],
                                ['type' => 'gridBuilderColumn', 'attrs' => ['data-col-span' => null], 'content' => [['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => '3']]]]],
                                ['type' => 'gridBuilderColumn', 'attrs' => ['data-col-span' => null], 'content' => [['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => '4']]]]],
                                ['type' => 'gridBuilderColumn', 'attrs' => ['data-col-span' => null], 'content' => [['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => '5']]]]],
                            ],
                        ],
                    ],
                ],
            ]);

            $migrate = Artisan::call('migrate', ['--path' => 'app-modules/resource-hub/database/migrations/2026_04_13_000000_tmp_data_migrate_resource_hub_articles_to_rich_editor_format.php']);

            expect($migrate)->toBe(Command::SUCCESS);

            $content = json_decode((string) DB::table('resource_hub_articles')->where('id', $item->id)->value('article_details'), associative: true); /** @phpstan-ignore-line */

            /** @phpstan-ignore-next-line */
            expect($content['content'][0]['type'])->toBe('grid');
            /** @phpstan-ignore-next-line */
            expect($content['content'][0]['attrs']['data-cols'])->toBe('5');
            /** @phpstan-ignore-next-line */
            expect($content['content'][0]['attrs']['data-from-breakpoint'])->toBe('sm');
            /** @phpstan-ignore-next-line */
            expect($content['content'][0]['content'][0]['type'])->toBe('gridColumn');
            /** @phpstan-ignore-next-line */
            expect($content['content'][0]['content'][0]['attrs']['data-col-span'])->toBe('1');
            /** @phpstan-ignore-next-line */
            expect(count($content['content'][0]['content']))->toBe(5);
        }
    );
});

test('2026_04_13_000000_tmp_data_migrate_resource_hub_articles_to_rich_editor_format transforms asymmetric grid', function () {
    isolatedMigration(
        '2026_04_13_000000_tmp_data_migrate_resource_hub_articles_to_rich_editor_format',
        function () {
            $item = ResourceHubArticle::factory()->createQuietly([
                'article_details' => [
                    'type' => 'doc',
                    'content' => [
                        [
                            'type' => 'grid',
                            'attrs' => ['type' => 'asymetric-left-thirds', 'cols' => '2'],
                            'content' => [
                                ['type' => 'gridColumn', 'content' => [['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => 'Left wide']]]]],
                                ['type' => 'gridColumn', 'content' => [['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => 'Right narrow']]]]],
                            ],
                        ],
                    ],
                ],
            ]);

            $migrate = Artisan::call('migrate', ['--path' => 'app-modules/resource-hub/database/migrations/2026_04_13_000000_tmp_data_migrate_resource_hub_articles_to_rich_editor_format.php']);

            expect($migrate)->toBe(Command::SUCCESS);

            $content = json_decode((string) DB::table('resource_hub_articles')->where('id', $item->id)->value('article_details'), associative: true); /** @phpstan-ignore-line */

            /** @phpstan-ignore-next-line */
            expect($content['content'][0]['attrs']['data-cols'])->toBe('3');
            /** @phpstan-ignore-next-line */
            expect($content['content'][0]['content'][0]['attrs']['data-col-span'])->toBe('2');
            /** @phpstan-ignore-next-line */
            expect($content['content'][0]['content'][1]['attrs']['data-col-span'])->toBe('1');
        }
    );
});

test('2026_04_13_000000_tmp_data_migrate_resource_hub_articles_to_rich_editor_format transforms checkedList', function () {
    isolatedMigration(
        '2026_04_13_000000_tmp_data_migrate_resource_hub_articles_to_rich_editor_format',
        function () {
            $item = ResourceHubArticle::factory()->createQuietly([
                'article_details' => [
                    'type' => 'doc',
                    'content' => [
                        [
                            'type' => 'checkedList',
                            'content' => [
                                ['type' => 'checkedListItem', 'attrs' => ['checked' => true], 'content' => [['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => 'Item 1']]]]],
                                ['type' => 'checkedListItem', 'attrs' => ['checked' => false], 'content' => [['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => 'Item 2']]]]],
                            ],
                        ],
                    ],
                ],
            ]);

            $migrate = Artisan::call('migrate', ['--path' => 'app-modules/resource-hub/database/migrations/2026_04_13_000000_tmp_data_migrate_resource_hub_articles_to_rich_editor_format.php']);

            expect($migrate)->toBe(Command::SUCCESS);

            $content = json_decode((string) DB::table('resource_hub_articles')->where('id', $item->id)->value('article_details'), associative: true); /** @phpstan-ignore-line */

            /** @phpstan-ignore-next-line */
            expect($content['content'][0]['type'])->toBe('bulletList');
            /** @phpstan-ignore-next-line */
            expect($content['content'][0]['content'][0]['type'])->toBe('listItem');
            /** @phpstan-ignore-next-line */
            expect($content['content'][0]['content'][0]['attrs'])->not->toHaveKey('checked');
            /** @phpstan-ignore-next-line */
            expect($content['content'][0]['content'][1]['type'])->toBe('listItem');
        }
    );
});

test('2026_04_13_000000_tmp_data_migrate_resource_hub_articles_to_rich_editor_format does not modify unchanged content', function () {
    isolatedMigration(
        '2026_04_13_000000_tmp_data_migrate_resource_hub_articles_to_rich_editor_format',
        function () {
            $originalContent = [
                'type' => 'doc',
                'content' => [
                    [
                        'type' => 'paragraph',
                        'content' => [['type' => 'text', 'text' => 'Simple text']],
                    ],
                ],
            ];

            $item = ResourceHubArticle::factory()->createQuietly([
                'article_details' => $originalContent,
            ]);

            $migrate = Artisan::call('migrate', ['--path' => 'app-modules/resource-hub/database/migrations/2026_04_13_000000_tmp_data_migrate_resource_hub_articles_to_rich_editor_format.php']);

            expect($migrate)->toBe(Command::SUCCESS);

            $content = json_decode((string) DB::table('resource_hub_articles')->where('id', $item->id)->value('article_details'), associative: true); /** @phpstan-ignore-line */
            expect($content)->toBe($originalContent);
        }
    );
});

test('2026_04_13_000000_tmp_data_migrate_resource_hub_articles_to_rich_editor_format transforms textStyle marks to textColor', function () {
    isolatedMigration(
        '2026_04_13_000000_tmp_data_migrate_resource_hub_articles_to_rich_editor_format',
        function () {
            $item = ResourceHubArticle::factory()->createQuietly([
                'article_details' => [
                    'type' => 'doc',
                    'content' => [
                        [
                            'type' => 'paragraph',
                            'content' => [
                                [
                                    'type' => 'text',
                                    'text' => 'Red text',
                                    'marks' => [
                                        [
                                            'type' => 'textStyle',
                                            'attrs' => ['color' => '#ff0000'],
                                        ],
                                    ],
                                ],
                                [
                                    'type' => 'text',
                                    'text' => ' and normal text',
                                ],
                            ],
                        ],
                    ],
                ],
            ]);

            $migrate = Artisan::call('migrate', ['--path' => 'app-modules/resource-hub/database/migrations/2026_04_13_000000_tmp_data_migrate_resource_hub_articles_to_rich_editor_format.php']);

            expect($migrate)->toBe(Command::SUCCESS);

            $content = json_decode((string) DB::table('resource_hub_articles')->where('id', $item->id)->value('article_details'), associative: true); /** @phpstan-ignore-line */

            /** @phpstan-ignore-next-line */
            expect($content['content'][0]['content'][0]['marks'][0]['type'])->toBe('textColor');
            /** @phpstan-ignore-next-line */
            expect($content['content'][0]['content'][0]['marks'][0]['attrs']['data-color'])->toBe('#ff0000');
            /** @phpstan-ignore-next-line */
            expect($content['content'][0]['content'][0]['marks'][0]['attrs'])->not->toHaveKey('color');
        }
    );
});
