<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LopHoc extends Model
{
    //
    use HasFactory;

    protected $table = 'lop_hocs';

    protected $fillable = [
        'ten_lop',
        'ma_lop',
        'si_so',
        'giao_vien',
        'so_dien_thoai_gvien',
        'ghi_chu',
        'trang_thai',
    ];

    public function sinhViens(): HasMany
    {
        return $this->hasMany(SinhVien::class);
    }
}
