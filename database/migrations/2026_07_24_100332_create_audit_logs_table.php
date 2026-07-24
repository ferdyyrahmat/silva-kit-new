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
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('user_name')->nullable();
            $table->string('event'); // e.g. login, logout, user.update, role.assign, profile.update, maintenance.toggle
            $table->string('action_description');
            $table->string('module')->default('system'); // system, user, role, profile, auth, maintenance
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->json('properties')->nullable(); // detailed changes or metadata
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
