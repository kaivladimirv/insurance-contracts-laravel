<?php

declare(strict_types=1);

namespace App\Http\Requests\ContractService;

use App\Enums\LimitType;
use App\Http\FormRequest;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\Rules\Enum;
use OpenApi\Attributes\QueryParameter;
use OpenApi\Attributes\Schema;

#[QueryParameter(
    parameter: 'contractServiceLimitType',
    name: 'limit_type',
    description: 'Limit type',
    schema: new Schema(type: 'number', enum: LimitType::class)
)]
#[QueryParameter(
    parameter: 'limitValueFrom',
    name: 'limit_value_from',
    description: 'Limit value from',
    schema: new Schema(type: 'number', format: 'double', minimum: 0)
)]
#[QueryParameter(
    parameter: 'limitValueTo',
    name: 'limit_value_to',
    description: 'Limit value to',
    schema: new Schema(type: 'number', format: 'double', minimum: 0)
)]
class IndexContractServiceRequest extends FormRequest
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
            'limit_type' => new Enum(LimitType::class),
            'limit_value_from' => 'numeric|min:0',
            'limit_value_to' => 'numeric|min:0'
        ];
    }
}
