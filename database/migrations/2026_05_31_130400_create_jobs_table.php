<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jobs', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('upwork_job_id', 100)->nullable()->unique();
            $table->string('title', 512);
            $table->string('url', 512)->unique();
            $table->longText('description')->nullable();
            $table->string('niche', 50);
            $table->string('budget_type', 20);
            $table->decimal('budget_min', 10, 2)->nullable();
            $table->decimal('budget_max', 10, 2)->nullable();
            $table->decimal('hourly_rate_min', 6, 2)->nullable();
            $table->decimal('hourly_rate_max', 6, 2)->nullable();
            $table->timestamp('posted_at')->nullable();
            $table->unsignedSmallInteger('proposals_count_at_time')->nullable();
            $table->string('difficulty', 20)->nullable();
            $table->json('required_skills')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->foreignUlid('employer_id')->nullable()->constrained('employers')->nullOnDelete();
            $table->timestamps();

            $table->index('niche');
            $table->index('budget_type');
            $table->index('posted_at');
            $table->index('employer_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jobs');
    }
};
