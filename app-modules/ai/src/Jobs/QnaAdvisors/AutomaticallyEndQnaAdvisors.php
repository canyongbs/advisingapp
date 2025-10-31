<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

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
*/

namespace AdvisingApp\Ai\Jobs\QnaAdvisors;

use AdvisingApp\Ai\Events\QnaAdvisors\EndQnaAdvisorThread;
use AdvisingApp\Ai\Models\QnaAdvisorThread;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;
use Throwable;

class AutomaticallyEndQnaAdvisors implements ShouldQueue
{
  use Dispatchable;
  use InteractsWithQueue;
  use Queueable;

    public function handle(): void
    {
        $threads = QnaAdvisorThread::query()
            ->whereNull('finished_at')
            ->whereHas(
                'latestMessage',
                fn (Builder $query) => $query->where('created_at', '<=', now()->subMinute())
            )
            ->get();

        $threads->each(function (QnaAdvisorThread $thread) {
          try {
            DB::beginTransaction();

            $thread->finished_at = now();
            $thread->save();

            event(new EndQnaAdvisorThread($thread));

            DB::commit();
          } catch (Throwable $error) {
            DB::rollBack();

            report($error);
          }
        });
    }
}
