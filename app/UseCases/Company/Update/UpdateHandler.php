<?php

declare(strict_types=1);

namespace App\UseCases\Company\Update;

use App\Models\Company;
use App\ReadModels\CompanyFetcher;
use App\UseCases\Command;
use App\UseCases\CommandHandler;
use Illuminate\Validation\ValidationException;

readonly class UpdateHandler implements CommandHandler
{
    /**
     * @psalm-api
     */
    public function __construct(private CompanyFetcher $fetcher)
    {
    }

    /**
     * @throws ValidationException
     */
    public function handle(UpdateCommand|Command $command): void
    {
        /** @var Company $company */
        $company = Company::query()->findOrFail($command->company_id);

        if ($company->name !== $command->name) {
            $this->assertNameIsUnique($command);
        }

        $company->fill($command->only(...$company->getFillable())->toArray());
        $company->save();
    }

    /**
     * @throws ValidationException
     */
    private function assertNameIsUnique(UpdateCommand $command): void
    {
        if ($this->fetcher->existsByName($command->name, $command->company_id)) {
            throw ValidationException::withMessages(['name' => __('Name already in use')]);
        }
    }
}
