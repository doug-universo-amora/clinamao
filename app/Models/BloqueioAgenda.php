<?php

namespace App\Models;

use App\Models\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BloqueioAgenda extends Model
{
    use HasFactory, BelongsToTenant;

    protected $table = 'bloqueios_agenda';

    protected $fillable = [
        'profissional_id',
        'cliente_id',
        'data_inicio',
        'data_fim',
        'motivo',
    ];

    protected $casts = [
        'data_inicio' => 'datetime',
        'data_fim' => 'datetime',
    ];

    // ─── Relationships ───────────────────────────────────────

    public function profissional()
    {
        return $this->belongsTo(Profissional::class, 'profissional_id');
    }
}
