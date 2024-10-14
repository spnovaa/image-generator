<?php

use App\Http\Controllers\Requests\ConvertorController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
Route::resource('converts', ConvertorController::class);
