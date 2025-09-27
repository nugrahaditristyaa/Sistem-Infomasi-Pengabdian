<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonitoringKpi extends Model
{
    use HasFactory;

    protected $table = 'monitoring_kpi';
    protected $primaryKey = 'id_monitoring';

    protected $fillable = [
        'id_kpi',
        'id_pengabdian',
        'tahun',
        'nilai_capai',
        'status',
    ];

    // Relationships
    public function pengabdian()
    {
        return $this->belongsTo(Pengabdian::class, 'id_pengabdian', 'id_pengabdian');
    }

    public function kpi()
    {
        return $this->belongsTo(Kpi::class, 'id_kpi', 'id_kpi');
    }
}
