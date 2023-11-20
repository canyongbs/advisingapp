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
<h3 class="mb-1 flex items-center text-lg font-semibold text-gray-900 dark:text-white">
    {{ $record->timelineRecordTitle() }}
</h3>
<time class="mb-2 block text-sm font-normal leading-none text-gray-400 dark:text-gray-500">
    {{ $record->timelineRecordDatetimeIndicator() }} {{ $record->timelineRecordDatetime() }}
</time>
<p class="mb-4 text-base font-normal text-gray-500 dark:text-gray-400">
    {{ $record->timelineRecordDescription() }}
</p>
