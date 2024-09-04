<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Http\FormRequest;
use Illuminate\Contracts\Validation\ValidationRule;
use OpenApi\Attributes\QueryParameter;
use OpenApi\Attributes\Schema;

#[QueryParameter(
    parameter: 'debtFrom',
    name: 'debt_from',
    description: 'Debt from',
    schema: new Schema(type: 'number')
)]
#[QueryParameter(
    parameter: 'debtTo',
    name: 'debt_to',
    description: 'Debt to',
    schema: new Schema(type: 'number')
)]
class DebtorsRequest extends FormRequest
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
            'service_id' => 'integer|exists:services,id',
            'debt_from' => 'integer|gt:0',
            'debt_to' => 'integer|gt:0'
        ];
    }
}
