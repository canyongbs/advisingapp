<div class="mt-4 w-full rounded-lg border-b border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800 lg:w-1/4">
    <div class="block items-center p-4 sm:flex">
        <div class="hidden items-center space-x-0 space-y-3 sm:flex sm:space-x-3 sm:space-y-0">
            <span class="font-normal text-gray-500 dark:text-gray-400 sm:text-xs md:text-sm">Show <span
                    class="font-semibold text-gray-900 dark:text-white"
                >TODO Pagination</span> of <span
                    class="font-semibold text-gray-900 dark:text-white">{{ $subscribedStudentsWithEngagements->count() }}</span></span>
        </div>
    </div>
    <div class="flex flex-col">
        <div class="overflow-x-auto">
            <div class="inline-block min-w-full align-middle">
                <div class="overflow-hidden shadow">
                    <table class="min-w-full table-fixed divide-y divide-gray-200">
                        <tbody class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-800">
                            @foreach ($subscribedStudentsWithEngagements as $student)
                                <tr class="cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700">
                                    <td class="flex items-center whitespace-nowrap p-4">
                                        <div class="text-base font-normal text-gray-700 dark:text-gray-400">
                                            {{ $student->display_name }}</div>
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
