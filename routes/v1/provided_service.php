<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

Route::get('', 'index')->name('index');
Route::post('', 'store')->name('store');
Route::get('/{provided_service_id}', 'show')->name('show');
Route::delete('/{provided_service_id}', 'destroy')->name('destroy');
