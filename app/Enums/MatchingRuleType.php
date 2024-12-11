<?php

namespace App\Enums;

enum MatchingRuleType: string
{
    case IN_DESCRIPTION = 'in_description';
    case IN_MODEL = 'in_model';
    case UNIT_PRICE = 'unit_price';

    public function label(): string
    {
        return match ($this) {
            self::IN_DESCRIPTION => __('Dopasowanie po zawartości ciągu w kolumnie opis'),
            self::IN_MODEL => __('Dopasowanie po zawartości ciągu w kolumnie model'),
            self::UNIT_PRICE => __('Dopasowanie po zakresie cenowym jednostkowej ceny zakupu'),
        };
    }
}
