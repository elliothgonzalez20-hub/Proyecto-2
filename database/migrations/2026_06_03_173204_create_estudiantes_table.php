<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\SoftDeletes;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('estudiantes', function (Blueprint $table) {
            $table->id();
            //$table->date('date_of_birth');//
            $table->string('name');
            $table->string('apellidos');
            $table->string('nacionalidad');
            $table->string('cedula');
            $table->string('nacimiento');
            $table->string('genero');
            $table->string('lugar');
            //$table->string('email');//
            ///$table->string('phone');//
            $table->foreignId('representante_id')
            ->nullable()
            ->constrained('Representantes')
            ->cascadeOnDelete();
            $table->timestamps();
        });
                {
                    Schema::table("estudiantes", function (Blueprint $table) {
                        $table->softDeletes();
                });
                }

      }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table("estudiantes", function (Blueprint $table) {
            $table->dropSoftDeletes();   });
        Schema::dropIfExists('estudiantes');
    }
};
