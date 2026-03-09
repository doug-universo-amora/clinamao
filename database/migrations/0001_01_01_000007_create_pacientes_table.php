<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pacientes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes')->cascadeOnDelete();
            $table->string('nome');
            $table->string('cpf', 14)->nullable();
            $table->string('telefone', 20)->nullable();
            $table->string('email')->nullable();
            $table->date('data_nascimento')->nullable();
            $table->text('observacoes')->nullable();
            $table->string('status')->default('ativo');
            $table->string('senha')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('cliente_id');
            $table->index('cpf');
            $table->unique(['cliente_id', 'cpf']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pacientes');
    }
};
