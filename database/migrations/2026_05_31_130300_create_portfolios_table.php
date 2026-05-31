<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('portfolios', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('title', 255);
            $table->text('description')->nullable();
            $table->string('loom_url', 512)->nullable();
            $table->string('live_url', 512)->nullable();
            $table->string('github_url', 512)->nullable();
            $table->json('tags');
            $table->json('tech_stack');
            $table->string('client_name')->nullable();
            $table->string('client_location')->nullable();
            $table->text('outcome_summary')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('portfolios');
    }
};
