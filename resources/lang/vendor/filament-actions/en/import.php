<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

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

return [
    'label' => 'Import',

    'modal' => [
        'heading' => 'Import :label',

        'form' => [
            'file' => [
                'label' => 'File',

                'placeholder' => 'Upload a CSV file',

                'rules' => [
                    'duplicate_columns' => '{0} The file must not contain more than one empty column header.|{1,*} The file must not contain duplicate column headers: :columns.',
                ],
            ],

            'columns' => [
                'label' => 'Columns',
                'placeholder' => 'Select a column',
            ],
        ],

        'actions' => [
            'download_example' => [
                'label' => 'Download example CSV file',
            ],

            'import' => [
                'label' => 'Import',
            ],
        ],
    ],

    'notifications' => [
        'completed' => [
            'title' => 'Import completed',

            'actions' => [
                'download_failed_rows_csv' => [
                    'label' => 'Download information about the failed row|Download information about the failed rows',
                ],
            ],
        ],

        'max_rows' => [
            'title' => 'Uploaded CSV file is too large',
            'body' => 'You may not import more than 1 row at once.|You may not import more than :count rows at once.',
        ],

        'started' => [
            'title' => 'Import started',
            'body' => 'Your import has begun and 1 row will be processed in the background.|Your import has begun and :count rows will be processed in the background.',
        ],
    ],

    'example_csv' => [
        'file_name' => ':importer-example',
    ],

    'failure_csv' => [
        'file_name' => 'import-:import_id-:csv_name-failed-rows',
        'error_header' => 'error',
        'system_error' => 'System error, please contact support.',
        'column_mapping_required_for_new_record' => 'The :attribute column was not mapped to a column in the file, but it is required for creating new records.',
    ],
];
