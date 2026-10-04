<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\LopHocController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\SinhVienController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

// Giữ đường dẫn thêm và các ví dụ định tuyến trong bài học cũ.
Route::prefix('sinhvien')->name('sinhvien.')->group(function () {
    Route::get('/', [SinhVienController::class, 'index'])->name('index');
    Route::get('/add', [SinhVienController::class, 'add'])->name('create');
    Route::post('/', [SinhVienController::class, 'store'])->name('store');
    Route::get('/show/{id?}', [SinhVienController::class, 'getID'])->whereNumber('id')->name('legacy-show');
    Route::get('/show2/{name?}/{tuoi?}', [SinhVienController::class, 'show2'])->whereNumber('tuoi')->name('demo');
    Route::get('/{sinh_vien}/edit', [SinhVienController::class, 'edit'])->whereNumber('sinh_vien')->name('edit');
    Route::get('/{sinh_vien}', [SinhVienController::class, 'show'])->whereNumber('sinh_vien')->name('show');
    Route::put('/{sinh_vien}', [SinhVienController::class, 'update'])->whereNumber('sinh_vien')->name('update');
    Route::delete('/{sinh_vien}', [SinhVienController::class, 'destroy'])->whereNumber('sinh_vien')->name('destroy');
});

Route::post('/lop-hocs/store', [LopHocController::class, 'store'])->name('lop-hocs.store');
Route::resource('lop-hocs', LopHocController::class)->names(['store' => 'lop-hocs.store-resource']);
Route::resource('menus', MenuController::class)->except('show');
