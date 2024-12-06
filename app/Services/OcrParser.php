<?php

namespace App\Services;

use App\Services\OcrParser\FormParser;
use App\Services\OcrParser\TableParser;

class OcrParser
{
    public static function getForms($ocrResponse)
    {
        return FormParser::parse($ocrResponse);
    }

    public static function getTables($ocrResponse)
    {
        return TableParser::parse($ocrResponse);
    }
}
