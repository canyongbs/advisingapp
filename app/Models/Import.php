<?php

/*
<COPYRIGHT>

Copyright © 2022-2023, Canyon GBS LLC

All rights reserved.

This file is part of a project developed using Laravel, which is an open-source framework for PHP.
Canyon GBS LLC acknowledges and respects the copyright of Laravel and other open-source
projects used in the development of this solution.

This project is licensed under the Affero General Public License (AGPL) 3.0.
For more details, see https://github.com/canyongbs/assistbycanyongbs/blob/main/LICENSE.

Notice:
- The copyright notice in this file and across all files and applications in this
 repository cannot be removed or altered without violating the terms of the AGPL 3.0 License.
- The software solution, including services, infrastructure, and code, is offered as a
 Software as a Service (SaaS) by Canyon GBS LLC.
- Use of this software implies agreement to the license terms and conditions as stated
 in the AGPL 3.0 License.

For more information or inquiries please visit our website at
https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace App\Models;

use App\Imports\Importer;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @mixin IdeHelperImport
 */
class Import extends BaseModel
{
    protected $casts = [
        'completed_at' => 'timestamp',
        'processed_rows' => 'integer',
        'total_rows' => 'integer',
        'successful_rows' => 'integer',
    ];

    public function failedRows(): HasMany
    {
        return $this->hasMany(FailedImportRow::class);
    }

    /**
     * @return BelongsTo<User, Import>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @param array<string, string> $columnMap
     * @param array<string, mixed> $options
     */
    public function getImporter(
        array $columnMap,
        array $options,
    ): Importer {
        return app($this->importer, [
            'import' => $this,
            'columnMap' => $columnMap,
            'options' => $options,
        ]);
    }

    public function getFailedRowsCount(): int
    {
        return $this->total_rows - $this->successful_rows;
    }
}
