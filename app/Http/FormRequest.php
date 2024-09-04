<?php

declare(strict_types=1);

namespace App\Http;

use Override;
use App\Models\Company;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class FormRequest extends \Illuminate\Foundation\Http\FormRequest
{
    /**
     * @psalm-api
     */
    public function authorize(): bool
    {
        return true;
    }

    public function company($guard = null): Company
    {
        return $this->user($guard);
    }

    #[Override]
    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->json([
            'errors' => $validator->errors()
        ], 422));
    }
}
