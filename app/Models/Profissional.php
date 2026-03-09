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

    /**
     * Profissionais para os quais ESTE profissional DEU acesso à própria agenda.
     * (A agenda é minha, e eu deixei as pessoas abaixo olharem)
     */
    public function acessosConcedidos()
    {
        return $this->belongsToMany(Profissional::class, 'profissional_acessos', 'concedente_id', 'acessante_id')
                    ->withPivot(['id', 'cliente_id', 'permissoes'])
                    ->withTimestamps();
    }

    /**
     * Profissionais (agendas) das quais ESTE profissional RECEBEU acesso.
     * (Essa pessoa me deu a chave pra eu olhar a agenda dela)
     */
    public function acessosRecebidos()
    {
        return $this->belongsToMany(Profissional::class, 'profissional_acessos', 'acessante_id', 'concedente_id')
                    ->withPivot(['id', 'cliente_id', 'permissoes'])
                    ->withTimestamps();
    }

    /**
     * Helper para Controle de Acesso Granular (Gatekeeping).
     * Retorna um array com os IDs dos profissionais cujas agendas ESTE profissional pode acessar,
     * dado o módulo (agenda, bloqueio, disponibilidade) e a ação (ler, criar, editar, excluir).
     */
    public function getIdsPermitidos(string $modulo, string $acao): array
    {
        $ids = [$this->id]; // Sempre tem acesso à própria agenda

        foreach ($this->acessosRecebidos as $acesso) {
            $json = $acesso->pivot->permissoes;
            $permissoes = is_string($json) ? json_decode($json, true) : ($json ?? []);

            if (isset($permissoes[$modulo][$acao]) && $permissoes[$modulo][$acao] === true) {
                $ids[] = $acesso->id;
            }
        }

        return array_unique($ids);
    }
}
