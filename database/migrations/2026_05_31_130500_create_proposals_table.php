<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('proposals', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('job_id')->constrained('jobs')->cascadeOnDelete();
            $table->foreignUlid('employer_id')->nullable()->constrained('employers')->nullOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('status', 30)->default('draft');
            $table->unsignedTinyInteger('connects_spent')->default(0);
            $table->decimal('bid_amount', 10, 2)->nullable();
            $table->decimal('bid_hourly_rate', 6, 2)->nullable();
            $table->text('cover_letter')->nullable();
            $table->string('loom_url', 512)->nullable();
            $table->unsignedSmallInteger('loom_view_count')->default(0);
            $table->timestamp('loom_viewed_at')->nullable();
            $table->boolean('loom_viewed')->default(false);
            $table->boolean('has_leverage')->default(false);
            $table->foreignUlid('leverage_portfolio_id')->nullable()->constrained('portfolios')->nullOnDelete();
            $table->text('leverage_notes')->nullable();
            $table->unsignedTinyInteger('ai_score')->nullable();
            $table->text('ai_score_reasoning')->nullable();
            $table->text('ai_script')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('replied_at')->nullable();
            $table->timestamp('interview_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->decimal('won_amount', 10, 2)->nullable();
            $table->string('loss_reason', 255)->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('sent_at');
            $table->index('user_id');
            $table->index('job_id');
            $table->index('ai_score');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('proposals');
    }
};
