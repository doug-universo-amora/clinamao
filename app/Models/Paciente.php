<?php

namespace App\Models;

use App\Enums\PacienteStatusEnum;
use App\Models\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Paciente extends Authenticatable
{
    use HasFactory, SoftDeletes, BelongsToTenant;

    protected $table = 'pacientes';

    protected $fillable = [
        'cliente_id',
        'nome',
        'cpf',
        'telefone',
        'email',
        'data_nascimento',
        'observacoes',
        'status',
        'senha',
    ];

    protected $hidden = [
        'senha',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'data_nascimento' => 'date',
            'status' => PacienteStatusEnum::class,
            'senha' => 'hashed',
        ];
    }

    /**
     * Get the password for authentication (maps 'senha' column).
     */
    public function getAuthPassword(): string
    {
        return $this->senha;
    }

    // ─── Relationships ───────────────────────────────────────

    public function agendamentos()
    {
        return $this->hasMany(Agendamento::class, 'paciente_id');
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }
}
