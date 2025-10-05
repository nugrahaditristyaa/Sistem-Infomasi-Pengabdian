<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kpi extends Model
{
    use HasFactory;

    protected $table = 'kpi';
    protected $primaryKey = 'id_kpi';

    protected $fillable = [
        'kode',
        'indikator',
        'target',
        'satuan'
    ];

    protected $casts = [
        'target' => 'integer'
    ];

    // Relationships
    public function monitoringKpi()
    {
        return $this->hasMany(MonitoringKpi::class, 'id_kpi', 'id_kpi');
    }
}

