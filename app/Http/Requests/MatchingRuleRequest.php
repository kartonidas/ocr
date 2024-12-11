<?php

namespace App\Http\Requests;

use App\Enums\MatchingRuleType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MatchingRuleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'product_id' => ['required', 'integer'],
            'type' => ['required', Rule::enum(MatchingRuleType::class)],
            'rule' => ['required', 'string'],
            'priority' => ['required', 'integer', 'gte:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'product_id.required' => __('Uzupełnij pole identyfikator produktu.'),
            'product_id.integer' => __('Wartość w polu identyfikator produktu musi byc liczbą całkowitą.'),
            'type.required' => __('Wybierz pole rodzaj dopasowania.'),
            'type.enum' => __('Uzupełnij rodzaj reguły.'),
            'rule.required' => __('Uzupełnij tekst dopasowania.'),
            'priority.required' => __('Uzupełnij priorytet.'),
            'priority.integer' => __('Wartość w polu priorytet musi byc liczbą całkowitą.'),
            'priority.gte' => __('Wartość w polu priorytet musi być większa bądź równia :value.'),
        ];
    }
}
