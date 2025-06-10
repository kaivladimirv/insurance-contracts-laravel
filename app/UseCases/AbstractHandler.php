<?php

declare(strict_types=1);

namespace App\UseCases;

use Illuminate\Database\Eloquent\Model;
use Spatie\LaravelData\Data;

readonly abstract class AbstractHandler implements CommandHandler
{
    protected function extractFillableData(Data $command, Model $model): array
    {
        return $command->only(...$model->getFillable())->toArray();
    }
}
