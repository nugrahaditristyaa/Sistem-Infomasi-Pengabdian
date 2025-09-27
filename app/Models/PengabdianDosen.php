<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengabdianDosen extends Model
{
    use HasFactory;

    protected $table = 'pengabdian_dosen';

    protected $fillable = [
        'id_pengabdian',
        'nik',
        'status_anggota'
    ];

    // Relationships
    public function pengabdian()
    {
        return $this->belongsTo(Pengabdian::class, 'id_pengabdian', 'id_pengabdian');
    }

    public function dosen()
    {
        return $this->belongsTo(Dosen::class, 'nik', 'nik');
    }
}

