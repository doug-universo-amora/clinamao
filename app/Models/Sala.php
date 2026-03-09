<?php

namespace App\Models;

use App\Models\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sala extends Model
{
    use HasFactory, BelongsToTenant;

    protected $table = 'salas';

    protected $fillable = [
        'cliente_id',
        'nome',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    // ─── Relationships ───────────────────────────────────────

    public function agendamentos()
    {
        return $this->hasMany(Agendamento::class, 'sala_id');
    }
}
