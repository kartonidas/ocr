<?php

namespace App\Jobs;

use App\Enums\OcrDocumentStatus;
use App\Models\OcrDocument;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;

class OcrJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public OcrDocument $document)
    {
    }

    public function handle(): void
    {
        $this->document->status = OcrDocumentStatus::PENDING;
        $this->document->save();


    }

    public function failed(Throwable $e)
    {

    }

    public function uniqueId(): string
    {
        return $this->document->id;
    }
}
