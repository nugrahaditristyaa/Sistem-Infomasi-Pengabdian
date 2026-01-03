<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dosen extends Model
{
    use HasFactory;

    protected $table = 'dosen';
    protected $primaryKey = 'nik';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'nik',
        'nama',
        'nidn',
        'jabatan',
        'prodi',
        'bidang_keahlian',
        'email'
    ];

    // Scopes
    /**
     * Scope to filter only FTI lecturers (Informatika and Sistem Informasi)
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFti($query)
    {
        return $query->whereIn('prodi', ['Informatika', 'Sistem Informasi']);
    }

    // Relationships
    public function pengabdianDosen()
    {
        return $this->hasMany(PengabdianDosen::class, 'nik', 'nik');
    }

    public function pengabdian()
    {
        return $this->belongsToMany(Pengabdian::class, 'pengabdian_dosen', 'nik', 'id_pengabdian')
            ->withPivot('status_anggota')
            ->withTimestamps();
    }

    /**
     * Mendefinisikan relasi Many-to-Many ke model DetailHki
     * melalui tabel pivot 'anggota_hki'.
     */
    public function detailHki()
    {
        return $this->belongsToMany(
            DetailHki::class,       // 1. Model tujuan
            'anggota_hki',          // 2. Nama tabel pivot
            'nik',                  // 3. Foreign key di pivot untuk model ini (Dosen)
            'id_detail_hki'         // 4. Foreign key di pivot untuk model tujuan
        );
    }

    public function pengabdianSebagaiAnggota()
    {
        return $this->belongsToMany(Pengabdian::class, 'pengabdian_dosen', 'nik', 'id_pengabdian')
            ->withTimestamps();
    }
}
