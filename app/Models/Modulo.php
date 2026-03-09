<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Modulo extends Model
{
    use HasFactory;

    protected $table = 'modulos';

    protected $fillable = [
        'nome',
        'slug',
        'descricao',
    ];

    // ─── Relationships ───────────────────────────────────────

    public function clientes()
    {
        return $this->belongsToMany(Cliente::class, 'cliente_modulo')
                     ->withPivot('ativo', 'ativado_em')
                     ->withTimestamps();
    }
}
