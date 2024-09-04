<?php

declare(strict_types=1);

use App\Http\Controllers\CompanyController;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\ContractServiceController;
use App\Http\Controllers\DebtorsController;
use App\Http\Controllers\InsuredPersonController;
use App\Http\Controllers\PersonController;
use App\Http\Controllers\ProvidedServiceController;
use App\Http\Controllers\ServiceController;
use Illuminate\Support\Facades\Route;

Route::controller(CompanyController::class)->name('company.')->prefix('company')->group(
    base_path('routes/v1/company.php')
);

Route::controller(PersonController::class)->name('persons.')->prefix('persons')
    ->whereNumber('person')->middleware('auth:sanctum')->group(
        base_path('routes/v1/person.php')
    );

Route::controller(ServiceController::class)->name('services.')->prefix('services')
    ->whereNumber('service')->middleware('auth:sanctum')->group(
        base_path('routes/v1/service.php')
    );

Route::controller(ContractController::class)->name('contracts.')->prefix('contracts')
    ->whereNumber('contract')->middleware('auth:sanctum')->group(
        base_path('routes/v1/contract.php')
    );

Route::controller(ContractServiceController::class)->name('contractServices.')->prefix('contracts/{contract_id}/services')
    ->whereNumber(['contract', 'contractService'])->middleware('auth:sanctum')->group(
        base_path('routes/v1/contract_service.php')
    );

Route::controller(InsuredPersonController::class)->name('insuredPerson.')->prefix('contracts/{contract_id}/insured_persons')
    ->whereNumber(['contract', 'insuredPerson'])->middleware('auth:sanctum')->group(
        base_path('routes/v1/insured_person.php')
    );

Route::controller(ProvidedServiceController::class)->name('providedServices.')->prefix('insured_persons/{insured_person_id}/provided_services')
    ->whereNumber(['insuredPerson', 'providedService'])->middleware('auth:sanctum')->group(
        base_path('routes/v1/provided_service.php')
    );

Route::controller(DebtorsController::class)->name('debtors.')->prefix('debtors')
    ->whereNumber('contract')->middleware('auth:sanctum')->group(
        base_path('routes/v1/debtors.php')
    );
