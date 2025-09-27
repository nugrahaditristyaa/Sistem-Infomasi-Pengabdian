<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mitra extends Model
{
    use HasFactory;

    protected $table = 'mitra';
    protected $primaryKey = 'id_mitra';

    protected $fillable = [
        'id_pengabdian',
        'nama_mitra',
        'lokasi_mitra'
    ];

    // Relationships
    public function pengabdian()
    {
        return $this->belongsTo(Pengabdian::class, 'id_pengabdian', 'id_pengabdian');
    }
}

