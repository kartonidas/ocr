<?php

use App\Enums\OcrDocumentType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ocr_document_texts', function (Blueprint $table) {
            $table->id();
            $table->integer('ocr_document_id')->index();
            $table->enum('type', array_column(OcrDocumentType::cases(), 'value'));
            $table->text('result')->nullable();
            $table->text('scores')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ocr_document_texts');
    }
};
