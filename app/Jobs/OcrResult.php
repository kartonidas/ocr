<?php

namespace App\Jobs;

use App\Models\OcrDocument;
use App\Services\Ocr;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUniqueUntilProcessing;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;

class OcrResult implements ShouldQueue, ShouldBeUniqueUntilProcessing
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public OcrDocument $document)
    {
    }

    public function handle(): void
    {
        try {
            Ocr::processResult($this->document);
        } catch (Throwable $e) {
            // obsługa bledow, powiadomienie mailowe, zapisanie loga
        }
    }

    public function failed(Throwable $e)
    {

    }

    public function uniqueId(): string
    {
        return $this->document->aws_job_id;
    }
}
