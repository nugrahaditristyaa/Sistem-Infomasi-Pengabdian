<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JenisDokumen extends Model
{
    use HasFactory;

    protected $table = 'jenis_dokumen';
    protected $primaryKey = 'id_jenis_dokumen';

    protected $fillable = [
        'nama_jenis_dokumen'
    ];

    // Relationships
    public function dokumen()
    {
        return $this->hasMany(Dokumen::class, 'id_jenis_dokumen', 'id_jenis_dokumen');
    }
}

