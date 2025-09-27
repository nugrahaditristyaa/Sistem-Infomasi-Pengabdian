<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailHki extends Model
{
    use HasFactory;

    protected $table = 'detail_hki';
    protected $primaryKey = 'id_detail_hki';

    protected $fillable = [
        'id_luaran',
        'no_pendaftaran',
        'tgl_permohonan',
        'judul_ciptaan',
        'pemegang_hak_cipta',
        'jenis_ciptaan'
    ];

    protected $casts = [
        'tgl_permohonan' => 'date'
    ];

    // Relationships
    public function luaran()
    {
        return $this->belongsTo(Luaran::class, 'id_luaran', 'id_luaran');
    }

    /**
     * Mendefinisikan relasi Many-to-Many ke model Dosen
     * melalui tabel pivot 'anggota_hki'.
     */
    public function dosen()
    {
        return $this->belongsToMany(
            Dosen::class,           // 1. Model tujuan
            'anggota_hki',          // 2. Nama tabel pivot
            'id_detail_hki',        // 3. Foreign key di pivot untuk model ini
            'nik'                   // 4. Foreign key di pivot untuk model tujuan
        );
    }

    public function dokumen()
    {
        return $this->hasOne(Dokumen::class, 'id_detail_hki', 'id_detail_hki');
    }
}
