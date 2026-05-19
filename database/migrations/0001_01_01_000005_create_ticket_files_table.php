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
        Schema::create('ticket_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained('tickets')->cascadeOnDelete();
            $table->string('original_name'); // Nombre original del archivo
            $table->string('file_name'); // Nombre guardado en servidor
            $table->string('file_path'); // Ruta del archivo
            $table->string('mime_type'); // Tipo MIME (application/pdf, image/jpeg, etc)
            $table->bigInteger('file_size'); // Tamaño en bytes
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_files');
    }
};
