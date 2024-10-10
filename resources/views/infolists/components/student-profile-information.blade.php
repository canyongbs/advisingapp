{{--
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

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
<div {{ $attributes }}>
    <div class="grid grid-cols-1 gap-8 md:grid-cols-2 lg:grid-cols-3">
        <div class="rounded-xl border bg-white">
            <div class="border-b px-6 py-4 text-lg font-medium text-black">
                {{ $getHeading() }}
            </div>
            <div class="text-lg font-medium text-black">
                <div class="border-b p-6">
                    <div>
                        <p class="mb-3 text-base font-medium text-black">Alternate Email</p>
                        <p class="mb-3 text-base text-gray-600">{{ $getState()?->email_2 }}</p>
                    </div>
                    <div>
                        <p class="mb-3 text-base font-medium text-black">Phone</p>
                        <p class="mb-3 text-base text-gray-600">{{ $getState()?->phone }}</p>
                    </div>
                    <div>
                        <p class="mb-3 text-base font-medium text-black">Address</p>
                        <p class="mb-3 text-base text-gray-600">{{ $getState()?->address }}</p>
                    </div>
                </div>
                <div class="border-b p-6">
                    <div>
                        <p class="mb-3 text-base font-medium text-black">Ethnicity</p>
                        <p class="mb-3 text-base text-gray-600">{{ $getState()?->ethnicity }}</p>
                    </div>
                    <div>
                        <p class="mb-3 text-base font-medium text-black">Birthdate</p>
                        <p class="mb-3 text-base text-gray-600">{{ $getState()?->birthdate }}</p>
                    </div>
                    <div>
                        <p class="mb-3 text-base font-medium text-black">High School Graduation</p>
                        <p class="mb-3 text-base text-gray-600">{{ $getState()?->hsgrad }}</p>
                    </div>
                </div>
                <div class="border-b p-6">
                    <div>
                        <p class="mb-3 text-base font-medium text-black">First Term</p>
                        <p class="mb-3 text-base text-gray-600">{{ $getState()?->f_e_term }}</p>
                    </div>
                    <div>
                        <p class="mb-3 text-base font-medium text-black">Recent Term</p>
                        <p class="mb-3 text-base text-gray-600">{{ $getState()?->mr_e_term }}</p>
                    </div>
                    <div>
                        <p class="mb-3 text-base font-medium text-black">SIS Holds</p>
                        <p class="mb-3 text-base text-gray-600">{{ $getState()?->holds }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{ $getChildComponentContainer() }}
</div>
