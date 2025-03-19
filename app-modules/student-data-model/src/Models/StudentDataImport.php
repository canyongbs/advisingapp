<?php

namespace AdvisingApp\StudentDataModel\Models;

use AdvisingApp\StudentDataModel\Enums\StudentDataImportStatus;
use App\Models\BaseModel;
use App\Models\Import;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentDataImport extends BaseModel
{
    public $fillable = [
        'job_batch_id',
        'started_at',
        'completed_at',
        'canceled_at',
    ];

    public $casts = [
        'completed_at' => 'immutable_datetime',
        'canceled_at' => 'immutable_datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function studentsImport(): BelongsTo
    {
        return $this->belongsTo(Import::class, 'students_import_id');
    }

    public function emailAddressesImport(): BelongsTo
    {
        return $this->belongsTo(Import::class, 'email_addresses_import_id');
    }

    public function phoneNumbersImport(): BelongsTo
    {
        return $this->belongsTo(Import::class, 'phone_numbers_import_id');
    }

    public function addressesImport(): BelongsTo
    {
        return $this->belongsTo(Import::class, 'addresses_import_id');
    }

    public function programsImport(): BelongsTo
    {
        return $this->belongsTo(Import::class, 'programs_import_id');
    }

    public function enrollmentsImport(): BelongsTo
    {
        return $this->belongsTo(Import::class, 'enrollments_import_id');
    }

    public function getStatus(): StudentDataImportStatus
    {
        return match (true) {
            filled($this->canceled_at) => StudentDataImportStatus::Canceled,
            filled($this->completed_at) => StudentDataImportStatus::Completed,
            filled($this->started_at) => StudentDataImportStatus::Processing,
            default => StudentDataImportStatus::Pending,
        };
    }
}
