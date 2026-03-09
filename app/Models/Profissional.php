<?php

namespace App\Models;

use App\Models\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Profissional extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant;

    protected $table = 'profissionais';

    protected $fillable = [
        'user_id',
        'cliente_id',
        'especialidade',
        'registro_conselho',
        'duracao_consulta',
        'status',
    ];

    protected $casts = [
        'duracao_consulta' => 'integer',
        'status' => 'boolean',
    ];

    // ─── Relationships ───────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function disponibilidades()
    {
        return $this->hasMany(Disponibilidade::class, 'profissional_id');
    }

    public function bloqueios()
    {
        return $this->hasMany(BloqueioAgenda::class, 'profissional_id');
    }

    public function agendamentos()
    {
        return $this->hasMany(Agendamento::class, 'profissional_id');
    }

    /**
     * Retorna a duração da consulta para um determinado dia,
     * priorizando a configuração da disponibilidade.
     */
    public function getDuracaoConsultaParaDia(int $diaSemana): int
    {
        $disponibilidade = $this->disponibilidades()
            ->where('dia_semana', $diaSemana)
            ->first();

        if ($disponibilidade && $disponibilidade->duracao_consulta) {
            return $disponibilidade->duracao_consulta;
        }

        return $this->duracao_consulta;
    }
}
