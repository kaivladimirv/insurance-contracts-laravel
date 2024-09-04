<?php

declare(strict_types=1);

namespace App\UseCases\Company\Update;

use App\UseCases\Command;
use OpenApi\Attributes\Property;
use OpenApi\Attributes\Schema;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Data;

#[Schema(
    schema: 'CompanyUpdateCommand',
    title: 'CompanyUpdateCommand',
    required: ['name'],
    properties: [
        new Property(
            property: 'name',
            description: 'Company name',
            type: 'string',
            default: '',
            maxLength: 255,
            example: 'New company name'
        )
    ]
)]
class UpdateCommand extends Data implements Command
{
    public function __construct(
        readonly public int $company_id,
        #[Max(255)]
        readonly public string $name
    ) {
    }
}
