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
        Schema::create('regra_tributarias', function (Blueprint $table) {
           $table->id();
            $table->foreignId('produto_id')->constrained('produtos')->onDelete('cascade');
            $table->string('ato_legal')->nullable();
            $table->decimal('mva_original', 10, 2)->nullable();
            $table->decimal('multiplicador_original', 10, 2)->nullable();
            $table->decimal('mva_ajustada', 10, 2)->nullable();
            $table->decimal('multiplicador_ajustado', 10, 2)->nullable();
            $table->decimal('aliquota_interna', 10, 2);
            $table->decimal('aliquota_interestadual', 10, 2);
            $table->string('descricao_extra')->nullable(); // Para casos como "Demais Casos" em autopeças
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('regra_tributarias');
    }
};
