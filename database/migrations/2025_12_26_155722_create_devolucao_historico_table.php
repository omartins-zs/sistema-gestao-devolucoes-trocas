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
        Schema::create('devolucao_historico', function (Blueprint $table) {
            $table->id();
            $table->foreignId('devolucao_id')->constrained('devolucoes')->onDelete('cascade');
            $table->enum('status_old', ['pendente', 'aprovada', 'recusada', 'concluida']);
            $table->enum('status_new', ['pendente', 'aprovada', 'recusada', 'concluida']);
            $table->foreignId('alterado_por')->nullable()->constrained('users')->onDelete('set null');
            $table->dateTime('data_alteracao')->useCurrent();
            $table->text('observacoes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('devolucao_historico');
    }
};
