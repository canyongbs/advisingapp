<?php

namespace AdvisingApp\Project\Models;

use AdvisingApp\Audit\Models\Concerns\Auditable as AuditableTrait;
use AdvisingApp\Project\Database\Factories\ProjectFileFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Prunable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class ProjectFile extends Model implements HasMedia, Auditable
{
    /** @use HasFactory<ProjectFileFactory> */
    use HasFactory;

    use HasUuids;
    use InteractsWithMedia;
    use AuditableTrait;
    use Prunable;

    protected $fillable = [
        'description',
        'retention_date',
    ];

    public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection('file')
            ->useDisk('s3')
            ->singleFile()
            ->acceptsMimeTypes([
                'image/png',
                'image/jpeg',
                'image/gif',
                'application/pdf',
                'application/msword',
                'text/csv',
                'application/vnd.ms-excel',
                'application/msexcel',
                'application/ms-excel',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'application/vnd.ms-powerpoint',
                'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                'text/plain',
                'audio/mpeg',
                'video/mp4',
                'application/x-zip-compressed',
                'application/zip',
                'application/x-zip',
            ]);
    }

    /**
     * Get the prunable query for the model.
     *
     * @return Builder<ProjectFile>
     */
    public function prunable(): Builder
    {
        return static::where(
            'retention_date',
            '<',
            now()->startOfDay(),
        );
    }

    /**
     * @return BelongsTo<Project, $this>
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id');
    }
}
