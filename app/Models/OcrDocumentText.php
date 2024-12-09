<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class OcrDocumentText extends Model
{
    protected $guarded = [];

    public function getResultAttribute($value)
    {
        return unserialize($value);
    }

    public function setResultAttribute($value)
    {
        $this->attributes['result'] = serialize($value);
    }

    public function getScoresAttribute($value)
    {
        return unserialize($value);
    }

    public function setScoresAttribute($value)
    {
        $this->attributes['scores'] = serialize($value);
    }

    public function prepareTable(): array
    {
        $out = [];

        $result = $this->result;

        $lengths = [];
        foreach ($result as $row) {
            foreach ($row as $cellIndex => $cell) {
                $length = Str::length($cell);

                if (! isset($lengths[$cellIndex])) {
                    $lengths[$cellIndex] = $length;
                } else {
                    if ($length > $lengths[$cellIndex]) {
                        $lengths[$cellIndex] = $length;
                    }
                }
            }
        }

        $lengths = array_map(fn ($l): int => min(($l * 8), 400), $lengths);

        return [
            'header' => array_shift($result),
            'rows' => $result,
            'lengths' => $lengths,
        ];
    }
}
