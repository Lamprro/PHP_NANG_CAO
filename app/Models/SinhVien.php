<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SinhVien extends Model
{
    protected $table = 'sinh_viens';

    protected $fillable = ['name', 'age', 'lop_hoc_id'];

    public function lopHoc(): BelongsTo
    {
        return $this->belongsTo(LopHoc::class);
    }
}
