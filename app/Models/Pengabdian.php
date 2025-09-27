<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengabdian extends Model
{
    use HasFactory;

    protected $table = 'pengabdian';
    protected $primaryKey = 'id_pengabdian';

    protected $fillable = [
        'judul_pengabdian',
        'tanggal_pengabdian',
        'ketua_pengabdian',
        'id_luaran_wajib'
    ];

    protected $casts = [
        'tanggal_pengabdian' => 'date'
    ];

    // Relationships
    public function pengabdianDosen()
    {
        return $this->hasMany(PengabdianDosen::class, 'id_pengabdian', 'id_pengabdian');
    }

    public function pengabdianMahasiswa()
    {
        return $this->hasMany(PengabdianMahasiswa::class, 'id_pengabdian', 'id_pengabdian');
    }

    public function dosen()
    {
        return $this->belongsToMany(Dosen::class, 'pengabdian_dosen', 'id_pengabdian', 'nik')
            ->withPivot('status_anggota')
            ->withTimestamps();
    }

    public function mahasiswa()
    {
        return $this->belongsToMany(Mahasiswa::class, 'pengabdian_mahasiswa', 'id_pengabdian', 'nim')
            ->withTimestamps();
    }

    public function mitra()
    {
        return $this->hasMany(Mitra::class, 'id_pengabdian', 'id_pengabdian');
    }

    public function dokumen()
    {
        return $this->hasMany(Dokumen::class, 'id_pengabdian', 'id_pengabdian');
    }

    public function sumberDana()
    {
        return $this->hasMany(SumberDana::class, 'id_pengabdian', 'id_pengabdian');
    }

    public function luaran()
    {
        return $this->hasMany(Luaran::class, 'id_pengabdian', 'id_pengabdian');
    }

    public function monitoringKpi()
    {
        return $this->hasMany(MonitoringKpi::class, 'id_pengabdian', 'id_pengabdian');
    }

    public function ketua()
    {
        return $this->belongsTo(Dosen::class, 'ketua_pengabdian', 'nik');
    }

    public function anggotaDosen()
    {
        return $this->belongsToMany(Dosen::class, 'pengabdian_dosen', 'id_pengabdian', 'nik')
            ->withTimestamps();
    }

    public function luaranWajib()
    {
        return $this->belongsTo(LuaranWajib::class, 'id_luaran_wajib', 'id_luaran_wajib');
    }
}
