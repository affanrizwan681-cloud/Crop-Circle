<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_availabilities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->enum('day_of_week', ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday']);
            $table->integer('available_quantity')->default(0);
            $table->integer('reserved_quantity')->default(0);
            $table->boolean('is_available')->default(true);
            $table->timestamps();

            $table->unique(['product_id', 'day_of_week']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_availabilities');
    }
};
