<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('website_feedback', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('submitter_role', 32)->index();
            $table->string('type', 32)->index();
            $table->string('subject', 180);
            $table->text('message');
            $table->unsignedTinyInteger('rating')->nullable();
            $table->string('status', 24)->default('new')->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('website_feedback');
    }
};