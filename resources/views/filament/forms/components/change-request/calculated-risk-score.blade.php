{{--
<COPYRIGHT>

    Copyright © 2022-2023, Canyon GBS LLC. All rights reserved.

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
--}}
@php
    
    $livewireData = $getLivewire()->data;
    $impact = $livewireData['impact'];
    $likelihood = $livewireData['likelihood'];
    
    $risk = !blank($impact) && !blank($likelihood) ? $impact * $likelihood : null;
    
    function getClassesFromRisk($value)
    {
        $classMap = [
            '1-4' => 'bg-green-400/10 border-green-500 text-green-500',
            '5-10' => 'bg-yellow-400/10 border-yellow-500 text-yellow-500',
            '11-16' => 'bg-orange-400/10 border-orange-500 text-orange-500',
            '17-25' => 'bg-red-400/10 border-red-600 text-red-600',
        ];
    
        foreach ($classMap as $range => $classes) {
            [$min, $max] = explode('-', $range);
            if ($value >= (int) $min && $value <= (int) $max) {
                return $classes;
            }
        }
    }
    
@endphp

<div>
    <div class="flex flex-col">
        <label class="fi-fo-field-wrp-label inline-flex items-center gap-x-3">
            <span class="text-sm font-medium leading-6 text-gray-950 dark:text-white">
                Risk Score
            </span>
        </label>

        <div class="mt-1">
            @if ($risk && $risk > 0 && $impact <= 5 && $likelihood <= 5)
                <div
                    class="{{ getClassesFromRisk($risk) }} mt-0 flex items-center justify-center rounded-xl border-2 p-4 text-lg">
                    {{ $risk }}
                </div>
            @else
                <div class="fi-fo-field-wrp-helper-text text-sm text-gray-500">
                    Please provide valid impact and likelihood values to calculate this change request's risk score.
                </div>
            @endif
        </div>
    </div>

</div>
