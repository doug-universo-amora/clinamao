<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('feriados', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes')->cascadeOnDelete();
            $table->string('nome');
            $table->date('data');
            $table->timestamps();

            $table->index('cliente_id');
            $table->unique(['cliente_id', 'data']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feriados');
    }
};
