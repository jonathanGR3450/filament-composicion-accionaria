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
        Schema::create('accionistas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tipo_persona_id')->constrained('tipo_personas');
            $table->string('numero_identificacion', 30)->nullable();
            $table->string('nombre');
            $table->double('participacion_accionaria')->nullable();
            $table->foreignId('id_padre')->nullable()->constrained('accionistas')->onDelete('cascade');
            $table->foreignId('empresa_id')->constrained('empresas');
            $table->boolean('estado')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accionistas');
    }
};
