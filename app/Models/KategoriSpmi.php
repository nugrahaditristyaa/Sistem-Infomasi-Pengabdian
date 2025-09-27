<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KategoriSpmi extends Model
{
    use HasFactory;

    protected $table = 'kategori_spmi';
    protected $primaryKey = 'id_kategori_spmi';

    protected $fillable = [
        'kode_spmi',
        'deskripsi'
    ];

    // Relationships
    // Ubah relasi menjadi many-to-many
    public function luaran()
    {
        return $this->belongsToMany(Luaran::class, 'luaran_kategori_spmi', 'id_kategori_spmi', 'id_luaran');
    }
}
