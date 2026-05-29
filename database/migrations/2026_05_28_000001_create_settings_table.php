<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->text('openai_api_key')->nullable();
            $table->string('ai_model')->default('gpt-5-mini');
            $table->string('channel_name')->nullable();
            $table->string('niche')->nullable();
            $table->string('tone')->nullable();
            $table->string('language')->nullable();
            $table->text('default_cta')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
