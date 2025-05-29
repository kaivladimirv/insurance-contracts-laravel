<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

Route::get('', 'index')->name('index');
Route::get('/{contract_id}', 'indexByContract')->name('byContract');
