@php
    use AdvisingApp\StudentDataModel\Models\Student;
    use Illuminate\Support\Carbon;
    use App\Settings\DisplaySettings;

    /** @var Student $student */

    $timezone = app(DisplaySettings::class)->getTimezone();
@endphp
<div class="flex-1">
    <p class="text-xs">
        Last Updated
        {{ $student->updated_at->setTimezone($timezone)->format('m/d/Y \a\t g:i A') }}
    </p>
</div>
