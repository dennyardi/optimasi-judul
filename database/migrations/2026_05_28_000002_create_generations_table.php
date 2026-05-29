<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('generations', function (Blueprint $table) {
            $table->id();
            $table->string('video_title');
            $table->text('short_description');
            $table->json('generated_titles');
            $table->longText('generated_description');
            $table->json('generated_tags');
            $table->json('generated_hashtags');
            $table->longText('generated_pin_comment');
            $table->string('ai_model');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('generations');
    }
};
