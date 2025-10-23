<?php

// database/migrations/xxxx_xx_xx_create_client_field_configurations_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('client_field_configurations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->onDelete('cascade');
            
            // Configuración para Cod1
            $table->boolean('cod1_is_visible')->default(false);
            $table->string('cod1_label')->nullable();

            // Configuración para Cod2
            $table->boolean('cod2_is_visible')->default(false);
            $table->string('cod2_label')->nullable();
            
            // Configuración para Cod3
            $table->boolean('cod3_is_visible')->default(false);
            $table->string('cod3_label')->nullable();

            // Configuración para Cod4
            $table->boolean('cod4_is_visible')->default(false);
            $table->string('cod4_label')->nullable();

            $table->timestamps();

            $table->unique('client_id'); // Asegura solo una configuración por cliente
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('client_field_configurations');
    }
};
