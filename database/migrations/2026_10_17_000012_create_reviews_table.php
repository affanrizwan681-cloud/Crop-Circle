<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('farmer_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->unsignedTinyInteger('rating');
            $table->text('comment');
            $table->text('farmer_reply')->nullable();
            $table->timestamp('replied_at')->nullable();
            $table->boolean('is_moderated')->default(true)->index();
            $table->timestamps();

            $table->index(['farmer_id', 'is_moderated']);
            $table->index(['product_id', 'is_moderated']);
            $table->unique(['customer_id', 'order_id', 'product_id'], 'unique_verified_review');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
