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
<div class="flex w-full flex-col">

    <div class="mt-4 flex w-full justify-center">
        <img
            class="hidden h-5 dark:block"
            src="{{ Vite::asset('resources/images/default-logo-dark.png') }}"
            alt="{{ config('app.name') }}"
        />
        <img
            class="block h-5 dark:hidden"
            src="{{ Vite::asset('resources/images/default-logo-light.png') }}"
            alt="{{ config('app.name') }}"
        />
    </div>

    <div class="flex w-full justify-center pb-4 pt-2">
        <span class="w-11/12 text-center text-xs lg:w-3/4 xl:w-7/12">
            © 2023 Canyon GBS LLC. All Rights Reserved. Canyon GBS™, Advanced Student Support & Interaction Servicing
            Technology™, ASSIST by Canyon GBS™ are trademarks of Canyon GBS LLC. For more information or inquiries,
            visit
            our website at <a
                class="text-blue-600 underline dark:text-blue-400"
                href="https://canyongbs.com/"
            >https://canyongbs.com/.</a>
        </span>
    </div>

</div>
