<?php

namespace App\Enums;

enum OcrDocumentStatus: string
{
    case NEW = 'new';
    case PENDING = 'pending';
    case ERROR = 'error';
    case COMPLETED = 'completed';

    public function label(): string {
        return match ($this) {
            self::NEW => __('Oczekuje'),
            self::PENDING => __('Przetwarzanie'),
            self::ERROR => __('Błąd'),
            self::COMPLETED => __('Zakończone'),
        };
    }
}
