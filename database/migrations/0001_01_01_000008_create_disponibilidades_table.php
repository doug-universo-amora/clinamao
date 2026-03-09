<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('disponibilidades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('profissional_id')->constrained('profissionais')->cascadeOnDelete();
            $table->foreignId('cliente_id')->constrained('clientes')->cascadeOnDelete();
            $table->unsignedTinyInteger('dia_semana'); // 0=Dom, 1=Seg, ..., 6=Sab
            $table->time('hora_inicio');
            $table->time('hora_fim');
            $table->unsignedInteger('duracao_consulta')->nullable(); // nullable = usa do profissional
            $table->timestamps();

            $table->index('cliente_id');
            $table->index(['profissional_id', 'dia_semana']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('disponibilidades');
    }
};
