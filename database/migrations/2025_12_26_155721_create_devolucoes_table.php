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
        Schema::create('devolucoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pedido_item_id')->constrained('pedido_items')->onDelete('cascade');
            $table->foreignId('cliente_id')->constrained('clientes')->onDelete('cascade');
            $table->foreignId('produto_id')->constrained('produtos')->onDelete('cascade');
            $table->foreignId('produto_troca_id')->nullable()->constrained('produtos')->onDelete('set null');
            $table->integer('quantidade');
            $table->text('motivo');
            $table->enum('status', ['pendente', 'aprovada', 'recusada', 'concluida'])->default('pendente');
            $table->enum('tipo', ['devolucao', 'troca'])->default('devolucao');
            $table->dateTime('data_solicitacao')->useCurrent();
            $table->dateTime('data_status')->nullable();
            $table->text('observacoes')->nullable();
            $table->string('codigo_rastreamento', 50)->nullable()->unique();
            $table->text('motivo_troca')->nullable();
            $table->dateTime('data_envio')->nullable();
            $table->timestamps();
        });
        
        // Adiciona foreign key para devolucao_id na tabela pedidos (após criar devolucoes)
        if (Schema::hasTable('pedidos')) {
            Schema::table('pedidos', function (Blueprint $table) {
                $table->foreign('devolucao_id')->references('id')->on('devolucoes')->onDelete('set null');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('pedidos')) {
            Schema::table('pedidos', function (Blueprint $table) {
                $table->dropForeign(['devolucao_id']);
            });
        }
        
        Schema::dropIfExists('devolucoes');
    }
};
