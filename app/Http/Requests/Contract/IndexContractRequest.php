<?php

declare(strict_types=1);

namespace App\Http\Requests\Contract;

use App\Http\FormRequest;
use Illuminate\Contracts\Validation\ValidationRule;
use OpenApi\Attributes\QueryParameter;
use OpenApi\Attributes\Schema;

#[QueryParameter(
    parameter: 'contractNumber',
    name: 'number',
    description: 'Contract number',
    schema: new Schema(type: 'string', maxLength: 50)
)]
#[QueryParameter(
    parameter: 'periodFrom',
    name: 'period_from',
    description: 'Period from',
    schema: new Schema(type: 'string', format: 'date')
)]
#[QueryParameter(
    parameter: 'periodTo',
    name: 'period_to',
    description: 'Period to',
    schema: new Schema(type: 'string', format: 'date')
)]
#[QueryParameter(
    parameter: 'maxAmountFrom',
    name: 'max_amount_from',
    description: 'Max amount from',
    schema: new Schema(type: 'number', format: 'double', minimum: 0)
)]
#[QueryParameter(
    parameter: 'maxAmountTo',
    name: 'max_amount_to',
    description: 'Max amount tro',
    schema: new Schema(type: 'number', format: 'double', minimum: 0)
)]
class IndexContractRequest extends FormRequest
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
            'number' => 'string|max:50',
            'period_from' => 'date_format:Y-m-d',
            'period_to' => 'date_format:Y-m-d|after_or_equal:start_date',
            'max_amount_from' => 'numeric|min:0',
            'max_amount_to' => 'numeric|min:0'
        ];
    }
}
