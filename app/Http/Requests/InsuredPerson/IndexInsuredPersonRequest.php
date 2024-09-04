<?php

declare(strict_types=1);

namespace App\Http\Requests\InsuredPerson;

use App\Http\FormRequest;
use Illuminate\Contracts\Validation\ValidationRule;
use OpenApi\Attributes\QueryParameter;
use OpenApi\Attributes\Schema;

#[QueryParameter(
    parameter: 'policyNumber',
    name: 'policy_number',
    description: 'Policy number',
    schema: new Schema(type: 'string', maxLength: 30)
)]
#[QueryParameter(
    parameter: 'isAllowedToExceedLimit',
    name: 'is_allowed_to_exceed_limit',
    description: 'Is allowed to exceed limit',
    schema: new Schema(type: 'number', enum: [0, 1])
)]
class IndexInsuredPersonRequest extends FormRequest
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
            'policy_number' => 'max:30',
            'is_allowed_to_exceed_limit' => 'boolean'
        ];
    }
}
