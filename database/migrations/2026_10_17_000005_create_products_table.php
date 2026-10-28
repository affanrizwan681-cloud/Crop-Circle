<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('farmer_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('category_id')->constrained('categories')->cascadeOnDelete();
            $table->string('name');
            $table->string('slug')->index();
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->string('unit')->default('kg');
            $table->integer('stock_quantity')->default(0);
            $table->integer('weekly_quota')->default(0);
            $table->string('image')->nullable();
            $table->enum('status', ['available', 'sold_out', 'unavailable'])->default('available')->index();
            $table->boolean('is_moderated')->default(true)->index();
            $table->boolean('is_featured')->default(false)->index();
            $table->timestamps();

            $table->index(['farmer_id', 'category_id']);
            $table->index(['status', 'is_moderated']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
