<?php

declare(strict_types=1);

namespace App\Http\Requests\Contract;

use App\Enums\LimitType;
use App\Http\FormRequest;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\Rules\Enum;

class ShowProvidedServicesRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     * @psalm-api
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'page' => 'required|integer|min:1',
            'service_id' => 'integer',
            'service_name' => 'max:255',
            'date_of_service_from' => 'date_format:Y-m-d',
            'date_of_service_to' => 'date_format:Y-m-d',
            'limit_type' => new Enum(LimitType::class),
            'quantity_from' => 'numeric|gt:0',
            'quantity_to' => 'numeric|gt:0',
            'price_from' => 'numeric|gt:0',
            'price_to' => 'numeric|gt:0',
            'amount_from' => 'numeric|gt:0',
            'amount_to' => 'numeric|gt:0'
        ];
    }
}
