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
        Schema::create('secciones', function (Blueprint $table) {
            $table->id();
            $table->string('grado_ano'); 
            $table->string('letra'); 
            $table->integer('cupo_maximo')->default(35);
            $table->foreignId('profesor_guia_id')->nullable()->constrained('profesores')->nullOnDelete();
            $table->dropColumn('profesor_guia_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('secciones');
    }
};
