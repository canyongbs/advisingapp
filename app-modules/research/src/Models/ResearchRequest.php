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

namespace AdvisingApp\Research\Models;

use AdvisingApp\Ai\Enums\AiModel;
use AdvisingApp\Ai\Settings\AiResearchAssistantSettings;
use AdvisingApp\Research\Database\Factories\ResearchRequestFactory;
use App\Models\BaseModel;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

/**
 * @mixin IdeHelperResearchRequest
 */
class ResearchRequest extends BaseModel implements HasMedia
{
    /** @use HasFactory<ResearchRequestFactory> */
    use HasFactory;

    use InteractsWithMedia;

    protected $fillable = [
        'title',
        'topic',
        'results',
        'user_id',
        'started_at',
        'finished_at',
        'links',
        'research_model',
        'search_queries',
        'outline',
        'remaining_outline',
        'sources',
    ];

    protected $casts = [
        'started_at' => 'immutable_datetime',
        'finished_at' => 'immutable_datetime',
        'links' => 'array',
        'research_model' => AiModel::class,
        'search_queries' => 'array',
        'outline' => 'array',
        'remaining_outline' => 'array',
        'sources' => 'array',
    ];

    /**
     * @return HasMany<ResearchRequestQuestion, $this>
     */
    public function questions(): HasMany
    {
        return $this->hasMany(ResearchRequestQuestion::class);
    }

    /**
     * @return BelongsTo<ResearchRequestFolder, $this>
     */
    public function folder(): BelongsTo
    {
        return $this->belongsTo(ResearchRequestFolder::class, 'folder_id');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function hasStarted(): bool
    {
        if ($this->started_at) {
            return true;
        }

        if ($this->finished_at) {
            return true;
        }

        return filled($this->results);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('files')
            ->acceptsMimeTypes(config('ai.supported_file_types'));

        $this->addMediaCollection('header_image')
            ->singleFile();
    }

    /**
     * @return HasMany<ResearchRequestParsedFile, $this>
     */
    public function parsedFiles(): HasMany
    {
        return $this->hasMany(ResearchRequestParsedFile::class);
    }

    /**
     * @return HasMany<ResearchRequestParsedLink, $this>
     */
    public function parsedLinks(): HasMany
    {
        return $this->hasMany(ResearchRequestParsedLink::class);
    }

    /**
     * @return HasMany<ResearchRequestParsedSearchResults, $this>
     */
    public function parsedSearchResults(): HasMany
    {
        return $this->hasMany(ResearchRequestParsedSearchResults::class);
    }

    public function getProgress(): int
    {
        if (! $this->hasStarted()) {
            return 0;
        }

        if ($this->finished_at) {
            return $this->getProgressTotal();
        }

        $total = 0;

        $total += $this->parsedFiles()->count();

        $total += $this->parsedLinks()->count();

        if (filled($this->search_queries)) {
            $total++;
            $total += $this->parsedSearchResults()->count();
        }

        if (filled($this->outline['abstract']['heading'] ?? null)) {
            $total++;
        }

        if (filled($this->outline['introduction']['heading'] ?? null)) {
            $total++;
        }

        foreach ($this->outline['sections'] ?? [] as $section) {
            if (filled($section['heading'] ?? null)) {
                $total++;
            }

            foreach ($section['subsections'] ?? [] as $subsection) {
                if (filled($subsection['heading'] ?? null)) {
                    $total++;
                }
            }
        }

        if (filled($this->outline['conclusion']['heading'] ?? [])) {
            $total++;
        }

        if (filled($this->remaining_outline['abstract']['heading'] ?? null)) {
            $total--;
        }

        if (filled($this->remaining_outline['introduction']['heading'] ?? null)) {
            $total--;
        }

        foreach ($this->remaining_outline['sections'] ?? [] as $section) {
            if (filled($section['heading'] ?? null)) {
                $total--;
            }

            foreach ($section['subsections'] ?? [] as $subsection) {
                if (filled($subsection['heading'] ?? null)) {
                    $total--;
                }
            }
        }

        if (filled($this->remaining_outline['conclusion']['heading'] ?? [])) {
            $total--;
        }

        if (filled($this->title)) {
            $total++;
        }

        return $total;
    }

    public function getProgressTotal(): int
    {
        $total = 0;

        $total += $this->getMedia('files')->count();

        $total += count($this->links ?? []);

        // Search query generation
        $total++;

        if (filled($this->search_queries)) {
            $total += count($this->search_queries);
        } else {
            $total += app(AiResearchAssistantSettings::class)->reasoning_effort->getNumberOfSearchQueries();
        }

        if (filled($this->outline['abstract']['heading'] ?? null)) {
            $total++;
        }

        if (filled($this->outline['introduction']['heading'] ?? null)) {
            $total++;
        }

        foreach ($this->outline['sections'] ?? [] as $section) {
            if (filled($section['heading'] ?? null)) {
                $total++;
            }

            foreach ($section['subsections'] ?? [] as $subsection) {
                if (filled($subsection['heading'] ?? null)) {
                    $total++;
                }
            }
        }

        if (filled($this->outline['conclusion']['heading'] ?? [])) {
            $total++;
        }

        if (blank($this->outline)) {
            $total += 50;
        }

        // Title
        $total++;

        return $total;
    }

    public function getProgressPercentage(): float
    {
        if (! $this->hasStarted()) {
            return 0;
        }

        if ($this->finished_at) {
            return 100;
        }

        $progress = $this->getProgress();
        $progressTotal = $this->getProgressTotal();

        if ($progress > $progressTotal) {
            return 100;
        }

        return ($progress / $progressTotal) * 100;
    }
}
