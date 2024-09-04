<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

Route::get('', 'index')->name('index');
Route::get('/{contract}', 'indexByContract')->name('byContract');
