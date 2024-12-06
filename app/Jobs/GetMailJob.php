<?php

namespace App\Jobs;

use App\Enums\JobName;
use App\Models\JobLog;
use App\Services\Imap;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;

class GetMailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(Imap $imap): void
    {
        if (settings()->get('get-mail-auto')) {
            $imap->processEmails();

            JobLog::create(['job' => JobName::GET_MAIL, 'success' => true]);
        }
    }

    public function failed(Throwable $e)
    {
        JobLog::create(['job' => JobName::GET_MAIL, 'success' => false]);

        info($e);
    }
}
