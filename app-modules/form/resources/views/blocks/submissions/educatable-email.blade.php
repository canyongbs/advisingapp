{{--
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
--}}
@php
    use Assist\AssistDataModel\Models\Student;
    use Assist\AssistDataModel\Filament\Resources\StudentResource;
    use Assist\Prospect\Models\Prospect;
    use Assist\Prospect\Filament\Resources\ProspectResource;
@endphp

<x-form::blocks.field-wrapper
    class="py-3"
    :$label
    :$isRequired
>
    @if (filled($response ?? null))
        <div class="not-prose flex flex-wrap items-center gap-3">
            <span>{{ $response ?? null }}</span>
            @if ($authorType === Student::class)
                <a
                    href="{{ StudentResource::getUrl('view', ['record' => $authorKey]) }}"
                    target="_blank"
                >
                    <x-filament::badge color="success">
                        Student
                    </x-filament::badge>
                </a>
            @elseif ($authorType === Prospect::class)
                <a
                    href="{{ ProspectResource::getUrl('view', ['record' => $authorKey]) }}"
                    target="_blank"
                >
                    <x-filament::badge color="success">
                        Prospect
                    </x-filament::badge>
                </a>
            @else
                <x-filament::badge color="danger">
                    Not found
                </x-filament::badge>
            @endif
        </div>
    @else
        <span class="text-gray-500">No response</span>
    @endif
</x-form::blocks.field-wrapper>
