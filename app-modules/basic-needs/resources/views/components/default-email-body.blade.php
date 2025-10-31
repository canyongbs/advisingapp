<div>
    <p>Hi {{ $recipient?->full_name }},</p>

    <p>
        <strong>Program Name:</strong><br>
        {{ $record->name }}
    </p>

    <p>
        <strong>Description:</strong><br>
        {{ $record->description }}
    </p>

    <p>
        <strong>Program Category:</strong><br>
        {{ $record->basicNeedsCategories?->name }}
    </p>

    <p>
        <strong>Contact Person:</strong><br>
        {{ $record->contact_person }}
    </p>

    <p>
        <strong>Email Address:</strong><br>
        {{ $record->contact_email }}
    </p>

    <p>
        <strong>Contact Phone:</strong><br>
        {{ $record->contact_phone }}
    </p>

    <p>
        <strong>Location:</strong><br>
        {{ $record->location }}
    </p>

    <p>
        <strong>Availability:</strong><br>
        {{ $record->availability }}
    </p>

    <p>
        <strong>Eligibility Criteria:</strong><br>
        {{ $record->eligibility_criteria }}
    </p>

    <p>
        <strong>Application Process:</strong><br>
        {{ $record->application_process }}
    </p>
</div>
