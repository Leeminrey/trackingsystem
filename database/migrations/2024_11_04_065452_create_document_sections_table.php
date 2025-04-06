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
        Schema::create('document_sections', function (Blueprint $table) {
            $table->id(); // Auto-incrementing ID
            $table->foreignId('document_id')->constrained()->onDelete('cascade'); // Foreign key referencing documents
            $table->foreignId('section_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); 
            $table->timestamps(); // Optional: created_at and updated_at columns
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_sections');
    }
};
