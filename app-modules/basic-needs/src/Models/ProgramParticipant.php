<?php

namespace AdvisingApp\BasicNeeds\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProgramParticipant extends Model
{
    use HasFactory;

    protected $fillable = [
        'program_participants_type',
        'program_participants_id',
    ];

    public function program_participants(): MorphTo
    {
        return $this->morphTo();
    }

    public function basicNeedsPrograms(): BelongsTo
    {
        return $this->belongsTo(BasicNeedsProgram::class, 'basic_needs_program_id', 'id');
    }
}
