<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('modulos', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('slug')->unique();
            $table->text('descricao')->nullable();
            $table->timestamps();
        });

        Schema::create('cliente_modulo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes')->cascadeOnDelete();
            $table->foreignId('modulo_id')->constrained('modulos')->cascadeOnDelete();
            $table->boolean('ativo')->default(true);
            $table->timestamp('ativado_em')->nullable();
            $table->timestamps();

            $table->unique(['cliente_id', 'modulo_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cliente_modulo');
        Schema::dropIfExists('modulos');
    }
};
