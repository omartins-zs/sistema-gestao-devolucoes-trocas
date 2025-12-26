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
        Schema::create('reembolsos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('devolucao_id')->unique()->constrained('devolucoes')->onDelete('cascade');
            $table->foreignId('cliente_id')->constrained('clientes')->onDelete('cascade');
            $table->decimal('valor', 10, 2);
            $table->enum('status', ['pendente', 'processado', 'cancelado'])->default('pendente');
            $table->boolean('autorizado')->default(false);
            $table->foreignId('autorizado_por')->nullable()->constrained('users')->onDelete('set null');
            $table->dateTime('data_autorizacao')->nullable();
            $table->enum('metodo', ['credito_estorno', 'transferencia', 'boleto', 'pix'])->nullable();
            $table->text('observacoes')->nullable();
            $table->dateTime('data_processamento')->nullable();
            $table->foreignId('processado_por')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reembolsos');
    }
};
