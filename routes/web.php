<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SalesController;


Route::prefix('/sales')->controller(SalesController::class)->group(function () {
    Route::get('/', 'index')->name('sales.index');
    Route::post('/', 'store')->name('sales.store');
    Route::put('/', 'update')->name('sales.update');
    Route::delete('/', 'destroy')->name('sales.destroy');
});


Route::get('/', function () {
    return redirect()->route('sales.index');
});
