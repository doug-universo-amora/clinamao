<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cliente extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'clientes';

    protected $fillable = [
        'nome',
        'subdominio',
        'cnpj',
        'email',
        'telefone',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    // ─── Relationships ───────────────────────────────────────

    public function users()
    {
        return $this->hasMany(User::class, 'cliente_id');
    }

    public function profissionais()
    {
        return $this->hasMany(Profissional::class, 'cliente_id');
    }

    public function pacientes()
    {
        return $this->hasMany(Paciente::class, 'cliente_id');
    }

    public function agendamentos()
    {
        return $this->hasMany(Agendamento::class, 'cliente_id');
    }

    public function salas()
    {
        return $this->hasMany(Sala::class, 'cliente_id');
    }

    public function feriados()
    {
        return $this->hasMany(Feriado::class, 'cliente_id');
    }

    public function modulos()
    {
        return $this->belongsToMany(Modulo::class, 'cliente_modulo')
                     ->withPivot('ativo', 'ativado_em')
                     ->withTimestamps();
    }

    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class, 'cliente_id');
    }
}
