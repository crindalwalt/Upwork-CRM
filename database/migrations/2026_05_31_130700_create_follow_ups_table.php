<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('follow_ups', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('proposal_id')->constrained('proposals')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('type', 30);
            $table->timestamp('scheduled_at');
            $table->timestamp('completed_at')->nullable();
            $table->boolean('is_done')->default(false);
            $table->text('outcome_note')->nullable();
            $table->timestamps();

            $table->index('scheduled_at');
            $table->index('is_done');
            $table->index('proposal_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('follow_ups');
    }
};
