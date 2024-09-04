<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

Route::get('', 'index')->name('index');
Route::post('', 'store')->name('store');
Route::get('/{insuredPerson}', 'show')->name('show');
Route::post('/{insured_person_id}/update', 'update')->name('update');
Route::delete('/{insured_person_id}', 'destroy')->name('destroy');
Route::get('/{insuredPerson}/balance', 'showBalance')->name('showBalance');
