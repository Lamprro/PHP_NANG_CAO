<?php

namespace App\Http\Controllers;

use App\Models\LopHoc;
use App\Models\Menu;
use App\Models\SinhVien;

class HomeController extends Controller
{
    public function index()
    {
        $tongLop = LopHoc::count();
        $lopHoatDong = LopHoc::where('trang_thai', true)->count();
        $tongSinhVien = SinhVien::count();
        $tongMenu = Menu::count();
        $lopHocs = LopHoc::withCount('sinhViens')->latest('id')->limit(5)->get();

        return view('home', compact('tongLop', 'lopHoatDong', 'tongSinhVien', 'tongMenu', 'lopHocs'));
    }
}
