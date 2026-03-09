<?php

namespace App\Models;

use App\Enums\AgendamentoStatusEnum;
use App\Models\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Agendamento extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant;

    protected $table = 'agendamentos';

    protected $fillable = [
        'cliente_id',
        'profissional_id',
        'paciente_id',
        'sala_id',
        'data',
        'hora_inicio',
        'hora_fim',
        'hora_chegada',
        'status',
        'observacoes',
    ];

    protected function casts(): array
    {
        return [
            'data' => 'date',
            'status' => AgendamentoStatusEnum::class,
        ];
    }

    // ─── Relationships ───────────────────────────────────────

    public function profissional()
    {
        return $this->belongsTo(Profissional::class, 'profissional_id');
    }

    public function paciente()
    {
        return $this->belongsTo(Paciente::class, 'paciente_id');
    }

    public function sala()
    {
        return $this->belongsTo(Sala::class, 'sala_id');
    }

    public function historicoReagendamentos()
    {
        return $this->hasMany(HistoricoReagendamento::class, 'agendamento_id');
    }
}
