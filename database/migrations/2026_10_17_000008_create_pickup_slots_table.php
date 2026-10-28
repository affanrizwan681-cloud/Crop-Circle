<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pickup_slots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('farmer_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('market_id')->nullable()->constrained('markets')->nullOnDelete();
            $table->enum('day_of_week', ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday']);
            $table->time('start_time');
            $table->time('end_time');
            $table->time('cutoff_time')->nullable();
            $table->integer('max_orders')->default(15);
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();

            $table->index(['farmer_id', 'day_of_week']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pickup_slots');
    }
};
