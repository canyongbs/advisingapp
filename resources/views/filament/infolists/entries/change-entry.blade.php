<div class="px-4 sm:px-6 lg:px-8">
    <div class="mt-8 flow-root">
        <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
            <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 sm:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-300">
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6"
                                    scope="col"
                                >Value</th>
                                <th
                                    class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900"
                                    scope="col"
                                >Old</th>
                                <th
                                    class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900"
                                    scope="col"
                                ></th>
                                <th
                                    class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900"
                                    scope="col"
                                >New</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @foreach ($getState() as $value => $change)
                                <tr>
                                    <td class="py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">
                                        {{ $value }}</td>
                                    <td class="px-3 py-4 text-sm text-gray-500">
                                        {{ isset($change['old']) ? (is_array($change['old']) ? json_encode($change['old']) : $change['old']) : '' }}
                                    </td>
                                    <td class="px-3 py-4 text-sm text-gray-500"><x-heroicon-m-chevron-right /></td>
                                    <td class="px-3 py-4 text-sm text-gray-500">
                                        {{ isset($change['new']) ? (is_array($change['new']) ? json_encode($change['new']) : $change['new']) : '' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
