<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

Route::get('', 'index')->name('index');
Route::post('', 'store')->name('store');
Route::get('/{contractService}', 'show')->name('show');
Route::post('/{service_id}/update', 'update')->name('update');
Route::delete('/{service_id}', 'destroy')->name('destroy');
