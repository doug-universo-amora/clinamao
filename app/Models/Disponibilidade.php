<?php

namespace App\Models;

use App\Models\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Disponibilidade extends Model
{
    use HasFactory, BelongsToTenant;

    protected $table = 'disponibilidades';

    protected $fillable = [
        'profissional_id',
        'cliente_id',
        'dia_semana',
        'hora_inicio',
        'hora_fim',
        'duracao_consulta',
    ];

    protected $casts = [
        'dia_semana' => 'integer',
        'duracao_consulta' => 'integer',
    ];

    // ─── Relationships ───────────────────────────────────────

    public function profissional()
    {
        return $this->belongsTo(Profissional::class, 'profissional_id');
    }

    /**
     * Retorna a duração efetiva da consulta (prioriza valor da disponibilidade).
     */
    public function getDuracaoEfetivaAttribute(): int
    {
        return $this->duracao_consulta ?? $this->profissional->duracao_consulta;
    }
}
