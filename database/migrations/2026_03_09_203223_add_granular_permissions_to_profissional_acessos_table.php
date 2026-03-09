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
        Schema::table('profissional_acessos', function (Blueprint $table) {
            $table->dropColumn('nivel_acesso');
            $table->json('permissoes')->nullable()->after('acessante_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('profissional_acessos', function (Blueprint $table) {
            $table->dropColumn('permissoes');
            $table->string('nivel_acesso')->default('ver')->after('acessante_id');
        });
    }
};
