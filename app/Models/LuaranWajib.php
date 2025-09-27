<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LuaranWajib extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang terhubung dengan model.
     *
     * @var string
     */
    protected $table = 'luaran_wajib';

    /**
     * Primary key untuk model.
     *
     * @var string
     */
    protected $primaryKey = 'id_luaran_wajib';

    /**
     * Kolom yang dapat diisi secara massal.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nama_luaran',
    ];

    /**
     * Mendefinisikan relasi one-to-many ke model Pengabdian.
     * Satu Luaran Wajib bisa dimiliki oleh banyak Pengabdian.
     */
    public function pengabdian()
    {
        return $this->hasMany(Pengabdian::class, 'id_luaran_wajib', 'id_luaran_wajib');
    }
}

