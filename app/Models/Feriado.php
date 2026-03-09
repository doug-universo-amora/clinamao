<?php

namespace App\Models;

use App\Models\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Feriado extends Model
{
    use HasFactory, BelongsToTenant;

    protected $table = 'feriados';

    protected $fillable = [
        'cliente_id',
        'nome',
        'data',
    ];

    protected $casts = [
        'data' => 'date',
    ];
}
