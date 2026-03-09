<?php

namespace App\Models;

use App\Models\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistoricoReagendamento extends Model
{
    use HasFactory, BelongsToTenant;

    protected $table = 'historico_reagendamentos';

    protected $fillable = [
        'agendamento_id',
        'cliente_id',
        'data_anterior',
        'hora_anterior',
        'nova_data',
        'novo_horario',
        'usuario_id',
    ];

    protected $casts = [
        'data_anterior' => 'date',
        'nova_data' => 'date',
    ];

    // ─── Relationships ───────────────────────────────────────

    public function agendamento()
    {
        return $this->belongsTo(Agendamento::class, 'agendamento_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}
