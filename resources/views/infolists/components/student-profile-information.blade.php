<div {{ $attributes }}>
    <div class="grid lg:grid-cols-3 md:grid-cols-2 grid-cols-1 gap-8">
        <div class="border rounded-xl bg-white">
            <div class="px-6 py-4 text-black font-medium text-lg border-b">
                {{ $getHeading() }}
            </div>
            <div class="text-black font-medium text-lg">
                <div class="border-b p-6">
                    <div>
                        <p class="mb-3 text-black text-base font-medium">Alternate Email</p>
                        <p class="mb-3 text-gray-600 text-base">{{ $getState()?->email_2 }}</p>
                    </div>
                    <div>
                        <p class="mb-3 text-black text-base font-medium">Phone</p>
                        <p class="mb-3 text-gray-600 text-base">{{ $getState()?->phone }}</p>
                    </div>
                    <div>
                        <p class="mb-3 text-black text-base font-medium">Address</p>
                        <p class="mb-3 text-gray-600 text-base">{{ $getState()?->address }}</p>
                    </div>
                </div>
                <div class="border-b p-6">
                    <div>
                        <p class="mb-3 text-black text-base font-medium">Ethnicity</p>
                        <p class="mb-3 text-gray-600 text-base">{{ $getState()?->ethnicity }}</p>
                    </div>
                    <div>
                        <p class="mb-3 text-black text-base font-medium">Birthdate</p>
                        <p class="mb-3 text-gray-600 text-base">{{ $getState()?->birthdate }}</p>
                    </div>
                    <div>
                        <p class="mb-3 text-black text-base font-medium">High School Graduation</p>
                        <p class="mb-3 text-gray-600 text-base">{{ $getState()?->hsgrad }}</p>
                    </div>
                </div>
                <div class="border-b p-6">
                    <div>
                        <p class="mb-3 text-black text-base font-medium">First Term</p>
                        <p class="mb-3 text-gray-600 text-base">{{ $getState()?->f_e_term }}</p>
                    </div>
                    <div>
                        <p class="mb-3 text-black text-base font-medium">Recent Term</p>
                        <p class="mb-3 text-gray-600 text-base">{{ $getState()?->mr_e_term }}</p>
                    </div>
                    <div>
                        <p class="mb-3 text-black text-base font-medium">SIS Holds</p>
                        <p class="mb-3 text-gray-600 text-base">{{ $getState()?->holds }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{ $getChildComponentContainer() }}
</div>
