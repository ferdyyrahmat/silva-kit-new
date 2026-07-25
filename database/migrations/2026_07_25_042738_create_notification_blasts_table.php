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
        Schema::create('notification_blasts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('message');
            $table->json('channels'); // ["bell", "email", "whatsapp", "telegram"]
            $table->string('target_type')->default('all'); // all, role, user
            $table->unsignedBigInteger('target_id')->nullable();
            $table->string('type')->default('info');
            $table->string('status')->default('sent');
            $table->integer('sent_count')->default(0);
            $table->integer('failed_count')->default(0);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_blasts');
    }
};
