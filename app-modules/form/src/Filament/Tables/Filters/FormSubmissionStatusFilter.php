<?php

namespace AdvisingApp\Form\Filament\Tables\Filters;

use Illuminate\Database\Eloquent\Builder;
use AdvisingApp\Form\Enums\FormSubmissionStatus;
use App\Filament\Filters\OpenSearch\SelectFilter;

class FormSubmissionStatusFilter extends SelectFilter
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->options(FormSubmissionStatus::class);

        $this->query(function (array $data, Builder $query) {
            $value = $data['value'];

            if (blank($value)) {
                return;
            }

            if (! $value instanceof FormSubmissionStatus) {
                $value = FormSubmissionStatus::tryFrom($value);
            }

            match ($value) {
                FormSubmissionStatus::Requested => $query->requested(),
                FormSubmissionStatus::Submitted => $query->submitted(),
                FormSubmissionStatus::Canceled => $query->canceled(),
            };
        });
    }

    public static function getDefaultName(): ?string
    {
        return 'status';
    }
}
