<?php

use App\Http\Controllers\Api\SinhVienController;
use Illuminate\Support\Facades\Route;

Route::apiResource('sinh-viens', SinhVienController::class)
    ->parameters(['sinh-viens' => 'sinhVien']);
