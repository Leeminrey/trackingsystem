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
        Schema::create('documents', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('user_id'); // Foreign key
            $table->string('locator_no', 255)->unique(); // Locator Number
            $table->string('subject'); // Subject of the document
            $table->date('date_received'); // Manually entered date received
            $table->date('date_filed')->nullable(); // Automatically added date filed, nullable to avoid errors
            $table->text('details')->nullable(); // Details about the document
            $table->enum('status', ['approved', 'pending', 'rejected', 'pending in CL']); // Document status
            $table->text('file_path'); // File upload path
            $table->string('original_file_name'); // Store the original file name
            $table->string('hashed_file_name'); // Store the hashed file name
            $table->timestamps();   

            // Set foreign key constraint
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
