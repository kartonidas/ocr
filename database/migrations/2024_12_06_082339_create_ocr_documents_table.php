<?php

use App\Enums\OcrDocumentStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ocr_documents', function (Blueprint $table) {
            $table->id();
            $table->integer('mail_id');
            $table->string('mail_subject');
            $table->string('file');
            $table->enum('status', array_column(OcrDocumentStatus::cases(), 'value'))->default(OcrDocumentStatus::NEW);
            $table->string('aws_job_id')->nullable()->default(null);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ocr_documents');
    }
};
