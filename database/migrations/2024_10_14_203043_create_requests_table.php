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
        Schema::create('requests', function (Blueprint $table) {
            $table->id('id'); // BIGINT IDENTITY(1, 1)
            $table->string('email', 128); // VARCHAR(128) NOT NULL
            $table->string('status', 16); // VARCHAR(16) NOT NULL
            $table->string('caption', 512)->nullable(); // NVARCHAR(512) NULL
            $table->string('url', 4000)->nullable(); // NVARCHAR(4000) NULL
            $table->string('file_name', 4000)->nullable(); // NVARCHAR(4000) NULL
            $table->timestamps(6); // DATETIME2 NOT NULL, with precision for milliseconds

            $table->primary('id'); // Primary key constraint
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('requests');
    }
};
