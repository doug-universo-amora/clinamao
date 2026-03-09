<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('historico_reagendamentos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agendamento_id')->constrained('agendamentos')->cascadeOnDelete();
            $table->foreignId('cliente_id')->constrained('clientes')->cascadeOnDelete();
            $table->date('data_anterior');
            $table->time('hora_anterior');
            $table->date('nova_data');
            $table->time('novo_horario');
            $table->foreignId('usuario_id')->constrained('users');
            $table->timestamps();

            $table->index('cliente_id');
            $table->index('agendamento_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('historico_reagendamentos');
    }
};
