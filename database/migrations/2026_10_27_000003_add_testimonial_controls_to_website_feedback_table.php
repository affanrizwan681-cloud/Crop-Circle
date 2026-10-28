<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('website_feedback', function (Blueprint $table) {
            $table->boolean('public_consent')->default(false)->index();
            $table->boolean('is_featured')->default(false)->index();
        });
    }

    public function down(): void
    {
        Schema::table('website_feedback', function (Blueprint $table) {
            $table->dropIndex(['public_consent']);
            $table->dropIndex(['is_featured']);
            $table->dropColumn(['public_consent', 'is_featured']);
        });
    }
};