<?php

declare(strict_types=1);

namespace App\UseCases\Company\Update;

use App\ReadModels\CompanyFetcher;
use App\UseCases\Command;
use App\UseCases\CommandHandler;
use Illuminate\Validation\ValidationException;
use Override;

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
    #[Override]
    public function handle(UpdateCommand|Command $command): void
    {
        $company = $this->fetcher->getOne($command->company_id);

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
