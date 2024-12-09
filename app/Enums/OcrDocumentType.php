<?php

namespace App\Enums;

enum OcrDocumentType: string
{
    case FORMS = 'forms';
    case TABLES = 'tables';
}
