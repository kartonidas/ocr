<?php

namespace App\Models;

use App\Enums\OcrDocumentStatus;
use App\Enums\OcrDocumentType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OcrDocument extends Model
{
    public $guarded = [];

    public function casts() {
        return [
            'status' => OcrDocumentStatus::class,
        ];
    }

    public function texts(): HasMany
    {
        return $this->hasMany(OcrDocumentText::class);
    }

    public function getTables(): array
    {
        $tables = $this->texts()->where('type', OcrDocumentType::TABLES)->get();

        $out = [];
        foreach ($tables as $table) {
            $out[] = $table->prepareTable();
        }

        return $out;
    }

    public function saveTexts($type, $forms, $scores)
    {
        OcrDocumentText::create([
            'ocr_document_id' => $this->id,
            'type' => $type,
            'result' => $forms,
            'scores' => $scores,
        ]);
    }
}
