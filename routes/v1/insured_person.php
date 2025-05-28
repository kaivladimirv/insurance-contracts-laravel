<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

Route::get('', 'index')->name('index');
Route::post('', 'store')->name('store');
Route::get('/{insured_person_id}', 'show')->name('show');
Route::post('/{insured_person_id}/update', 'update')->name('update');
Route::delete('/{insured_person_id}', 'destroy')->name('destroy');
Route::get('/{insured_person_id}/balance', 'showBalance')->name('showBalance');
