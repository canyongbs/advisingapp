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
<div class="grid lg:grid-cols-3 md:grid-cols-2 grid-cols-1 gap-8">
        <div class="border rounded-xl bg-white">
            <div class="px-6 py-4 text-black font-medium text-lg border-b">
                Profile Information
            </div>
            <div class="text-black font-medium text-lg">
                <div class="border-b p-6">
                    <div>
                        <p class="mb-3 text-black text-base font-medium">Alternate Email</p>
                        <p class="mb-3 text-gray-600 text-base">{{ $record?->email_2 }}</p>
                    </div>
                    <div>
                        <p class="mb-3 text-black text-base font-medium">Phone</p>
                        <p class="mb-3 text-gray-600 text-base">{{ $record?->phone }}</p>
                    </div>
                    <div>
                        <p class="mb-3 text-black text-base font-medium">Address</p>
                        <p class="mb-3 text-gray-600 text-base">{{ $record?->address }}</p>
                    </div>
                </div>
                <div class="border-b p-6">
                    <div>
                        <p class="mb-3 text-black text-base font-medium">Ethnicity</p>
                        <p class="mb-3 text-gray-600 text-base">{{ $record?->ethnicity }}</p>
                    </div>
                    <div>
                        <p class="mb-3 text-black text-base font-medium">Birthdate</p>
                        <p class="mb-3 text-gray-600 text-base">{{ $record?->birthdate }}</p>
                    </div>
                    <div>
                        <p class="mb-3 text-black text-base font-medium">High School Graduation</p>
                        <p class="mb-3 text-gray-600 text-base">{{ $record?->hsgrad }}</p>
                    </div>
                </div>
                <div class="border-b p-6">
                    <div>
                        <p class="mb-3 text-black text-base font-medium">First Term</p>
                        <p class="mb-3 text-gray-600 text-base">{{ $record?->f_e_term }}</p>
                    </div>
                    <div>
                        <p class="mb-3 text-black text-base font-medium">Recent Term</p>
                        <p class="mb-3 text-gray-600 text-base">{{ $record?->mr_e_term }}</p>
                    </div>
                    <div>
                        <p class="mb-3 text-black text-base font-medium">SIS Holds</p>
                        <p class="mb-3 text-gray-600 text-base">{{ $record?->holds }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
