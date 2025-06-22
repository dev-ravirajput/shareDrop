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
        Schema::create('shares', function (Blueprint $table) {
        $table->id();
        $table->string('share_id')->unique();
        $table->enum('type', ['file', 'text']);
        $table->text('content')->nullable(); // For text shares
        $table->string('password')->nullable();
        $table->timestamp('expires_at')->nullable();
        $table->ipAddress('creator_ip')->nullable();
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shares');
    }
};
