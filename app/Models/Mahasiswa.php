<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mahasiswa extends Model
{
    use HasFactory;

    protected $table = 'mahasiswa';
    protected $primaryKey = 'nim';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'nim',
        'nama',
        'prodi'
    ];

    // Relationships
    public function pengabdianMahasiswa()
    {
        return $this->hasMany(PengabdianMahasiswa::class, 'nim', 'nim');
    }

    public function pengabdian()
    {
        return $this->belongsToMany(Pengabdian::class, 'pengabdian_mahasiswa', 'nim', 'id_pengabdian')
            ->withTimestamps();
    }
}








