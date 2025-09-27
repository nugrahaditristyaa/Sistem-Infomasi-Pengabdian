<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SumberDana extends Model
{
    use HasFactory;

    protected $table = 'sumber_dana';
    protected $primaryKey = 'id_sumber_dana';

    protected $fillable = [
        'id_pengabdian',
        'jenis',
        'nama_sumber',
        'jumlah_dana'
    ];

    protected $casts = [
        'jumlah_dana' => 'double'
    ];

    // Relationships
    public function pengabdian()
    {
        return $this->belongsTo(Pengabdian::class, 'id_pengabdian', 'id_pengabdian');
    }
}

