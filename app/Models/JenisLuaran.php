<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JenisLuaran extends Model
{
    use HasFactory;

    protected $table = 'jenis_luaran';
    protected $primaryKey = 'id_jenis_luaran';

    protected $fillable = [
        'nama_jenis_luaran'
    ];

    // Relationships
    public function luaran()
    {
        return $this->hasMany(Luaran::class, 'id_jenis_luaran', 'id_jenis_luaran');
    }
}

