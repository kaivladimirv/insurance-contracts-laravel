<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

Route::get('', 'index')->name('index');
Route::post('', 'store')->name('store');
Route::get('/{contract}', 'show')->name('show');
Route::post('/{id}/update', 'update')->name('update');
Route::delete('/{id}', 'destroy')->name('destroy');
Route::get('/{contract}/provided_services', 'showProvidedServices')->name('showProvidedServices');
