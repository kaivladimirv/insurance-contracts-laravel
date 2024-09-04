<?php

declare(strict_types=1);

namespace App\Http\Requests\Service;

use App\Http\FormRequest;
use Illuminate\Contracts\Validation\ValidationRule;

class IndexServiceRequest extends FormRequest
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
            'name' => 'max:255'
        ];
    }
}
