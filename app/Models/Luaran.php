<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Luaran extends Model
{
    use HasFactory;

    protected $table = 'luaran';
    protected $primaryKey = 'id_luaran';

    protected $fillable = [
        'id_pengabdian',
        'id_jenis_luaran'
    ];

    protected $casts = [
        'tahun' => 'integer'
    ];

    // Relationships
    public function pengabdian()
    {
        return $this->belongsTo(Pengabdian::class, 'id_pengabdian', 'id_pengabdian');
    }

    public function jenisLuaran()
    {
        return $this->belongsTo(JenisLuaran::class, 'id_jenis_luaran', 'id_jenis_luaran');
    }

    // Di dalam App\Models\Luaran.php
    public function detailHki()
    {
        return $this->hasOne(DetailHki::class, 'id_luaran', 'id_luaran');
    }
}
