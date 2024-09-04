<?php

declare(strict_types=1);

namespace App\Http\Requests\Person;

use App\Enums\NotifierType;
use App\Http\FormRequest;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\Rules\Enum;
use OpenApi\Attributes\QueryParameter;
use OpenApi\Attributes\Schema;

#[QueryParameter(
    parameter: 'lastName',
    name: 'last_name',
    description: 'Last name',
    schema: new Schema(type: 'string', maxLength: 255)
)]
#[QueryParameter(
    parameter: 'firstName',
    name: 'first_name',
    description: 'First name',
    schema: new Schema(type: 'string', maxLength: 255)
)]
#[QueryParameter(
    parameter: 'middleName',
    name: 'middle_name',
    description: 'Middle name',
    schema: new Schema(type: 'string', maxLength: 255)
)]
#[QueryParameter(
    parameter: 'email',
    name: 'email',
    description: 'Email',
    schema: new Schema(type: 'string', format: 'email', maxLength: 255)
)]
#[QueryParameter(
    parameter: 'phoneNumber',
    name: 'phone_number',
    description: 'Phone number',
    schema: new Schema(type: 'integer', maxLength: 15)
)]
#[QueryParameter(
    parameter: 'notifierType',
    name: 'notifier_type',
    description: 'Notifier type',
    schema: new Schema(type: 'integer', enum: NotifierType::class, nullable: true)
)]
class IndexPersonRequest extends FormRequest
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
            'last_name' => 'max:255',
            'first_name' => 'max:255',
            'middle_name' => 'max:255',
            'email' => 'max:255',
            'phone_number' => 'max:15',
            'notifier_type' => ['nullable', new Enum(NotifierType::class)]
        ];
    }
}
