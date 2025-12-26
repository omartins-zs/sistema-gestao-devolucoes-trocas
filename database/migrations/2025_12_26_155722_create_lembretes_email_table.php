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
        Schema::create('lembretes_email', function (Blueprint $table) {
            $table->id();
            $table->foreignId('devolucao_id')->constrained('devolucoes')->onDelete('cascade');
            $table->dateTime('data_envio')->useCurrent();
            $table->string('canal', 20)->default('email');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lembretes_email');
    }
};
