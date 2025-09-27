<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dokumen extends Model
{
    use HasFactory;

    protected $table = 'dokumen';
    protected $primaryKey = 'id_dokumen';

    // aktifkan timestamps
    public $timestamps = true;

    protected $fillable = [
        'id_pengabdian',
        'id_jenis_dokumen',
        'id_detail_hki',
        'nama_file',   // nama asli file (misal: proposal.pdf)
        'path_file'    // lokasi file di storage (misal: dokumen/proposal.pdf)
    ];

    // Relasi ke tabel pengabdian
    public function pengabdian()
    {
        return $this->belongsTo(Pengabdian::class, 'id_pengabdian', 'id_pengabdian');
    }

    // Relasi ke tabel jenis_dokumen
    public function jenisDokumen()
    {
        return $this->belongsTo(JenisDokumen::class, 'id_jenis_dokumen', 'id_jenis_dokumen');
    }

    // 🔥 Accessor untuk mendapatkan URL file langsung
    public function getUrlFileAttribute()
    {
        return asset('storage/' . $this->path_file);
    }

    public function detailHki()
    {
        return $this->belongsTo(DetailHki::class, 'id_detail_hki', 'id_detail_hki');
    }
}
