<?php

namespace App\Enums;

enum JobName: string
{
    case GET_MAIL = 'get_mail';

    public function label(): string
    {
        return match ($this) {
            self::GET_MAIL => 'Pobranie wiadomości e-mail',
        };
    }
}
