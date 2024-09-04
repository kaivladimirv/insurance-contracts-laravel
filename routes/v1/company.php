<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

Route::post('', 'register')->name('register');
Route::patch('/confirm/{emailConfirmToken}', 'confirm')->name('confirm');
Route::patch('/token', 'token')->name('token')->middleware('auth.basic');
Route::get('', 'show')->name('show')->middleware('auth:sanctum');
Route::post('/update', 'update')->name('update')->middleware('auth:sanctum');
Route::post('/password', 'changePassword')->name('changePassword')->middleware('auth.basic');
Route::post('/email', 'changeEmail')->name('changeEmail')->middleware('auth.basic');
Route::patch('/email/confirm/{newEmailConfirmToken}', 'confirmEmailChange')->name('confirmEmailChange')->middleware('auth.basic');
