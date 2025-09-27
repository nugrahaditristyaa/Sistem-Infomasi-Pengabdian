<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnggotaHki extends Model
{
    use HasFactory;

    protected $table = 'anggota_hki';
    protected $primaryKey = 'id_anggota_hki';

    protected $fillable = [
        'id_detail_hki',
        'nik',
        'peran'
    ];

    // Relationships
    public function detailHki()
    {
        return $this->belongsTo(DetailHki::class, 'id_detail_hki', 'id_detail_hki');
    }

    public function dosen()
    {
        return $this->belongsTo(Dosen::class, 'nik', 'nik');
    }
}

