<?php

namespace App\Models;

use App\Enums\OcrDocumentStatus;
use Illuminate\Database\Eloquent\Model;

class OcrDocument extends Model
{
    public $guarded = [];

    public function casts() {
        return [
            'status' => OcrDocumentStatus::class,
        ];
    }
}
