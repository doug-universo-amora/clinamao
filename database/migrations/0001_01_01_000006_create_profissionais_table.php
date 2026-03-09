<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('profissionais', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('cliente_id')->constrained('clientes')->cascadeOnDelete();
            $table->string('especialidade');
            $table->string('registro_conselho', 100)->nullable();
            $table->unsignedInteger('duracao_consulta')->default(30);
            $table->boolean('status')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index('cliente_id');
            $table->index(['cliente_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('profissionais');
    }
};
