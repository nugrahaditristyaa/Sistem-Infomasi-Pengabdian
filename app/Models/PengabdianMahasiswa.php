<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengabdianMahasiswa extends Model
{
    use HasFactory;

    protected $table = 'pengabdian_mahasiswa';
    protected $primaryKey = 'id_pengabdian_mahasiswa';

    protected $fillable = [
        'id_pengabdian',
        'nim'
    ];

    // Relationships
    public function pengabdian()
    {
        return $this->belongsTo(Pengabdian::class, 'id_pengabdian', 'id_pengabdian');
    }

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class, 'nim', 'nim');
    }
}
