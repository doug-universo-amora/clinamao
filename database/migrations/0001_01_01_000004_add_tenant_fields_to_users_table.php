<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('cliente_id')->after('id')->constrained('clientes')->cascadeOnDelete();
            $table->string('cpf', 14)->nullable()->after('name');
            $table->string('telefone', 20)->nullable()->after('email');
            $table->boolean('status')->default(true)->after('password');
            $table->timestamp('ultimo_login')->nullable()->after('status');
            $table->softDeletes();

            $table->index('cliente_id');
            $table->unique(['cliente_id', 'cpf']);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['cliente_id']);
            $table->dropUnique(['cliente_id', 'cpf']);
            $table->dropColumn(['cliente_id', 'cpf', 'telefone', 'status', 'ultimo_login', 'deleted_at']);
        });
    }
};
