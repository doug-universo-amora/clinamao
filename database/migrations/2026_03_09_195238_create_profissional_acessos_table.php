<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('profissional_acessos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes')->cascadeOnDelete();
            
            // O profissional que é dono da agenda e ESTÁ DANDO acesso a outrem
            $table->foreignId('concedente_id')->constrained('profissionais')->cascadeOnDelete();
            
            // O profissional que ESTÁ RECEBENDO permissão para acessar a agenda do concedente
            $table->foreignId('acessante_id')->constrained('profissionais')->cascadeOnDelete();
            
            // Nível de acesso (ver, gerenciar)
            $table->string('nivel_acesso')->default('ver'); // 'ver', 'gerenciar', etc.
            
            $table->timestamps();

            // Garantir que não exista acesso duplicado
            $table->unique(['concedente_id', 'acessante_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profissional_acessos');
    }
};
