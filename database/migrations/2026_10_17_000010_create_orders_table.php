<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->foreignId('parent_order_id')->nullable()->constrained('orders')->nullOnDelete();
            $table->foreignId('customer_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('farmer_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('market_id')->nullable()->constrained('markets')->nullOnDelete();
            $table->foreignId('pickup_slot_id')->nullable()->constrained('pickup_slots')->nullOnDelete();
            $table->date('pickup_date');
            $table->string('pickup_time');
            $table->decimal('total_amount', 10, 2)->default(0.00);
            $table->enum('status', ['pending', 'accepted', 'ready_for_pickup', 'completed', 'cancelled', 'rejected'])->default('pending')->index();
            $table->string('payment_method')->default('cash_on_pickup');
            $table->enum('payment_status', ['unpaid', 'paid'])->default('unpaid')->index();
            $table->text('customer_name')->nullable();
            $table->text('customer_phone')->nullable();
            $table->text('notes')->nullable();
            $table->text('decline_reason')->nullable();
            $table->timestamps();

            $table->index(['customer_id', 'status']);
            $table->index(['farmer_id', 'status']);
            $table->index('pickup_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
