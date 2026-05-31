<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('proposal_notes', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('proposal_id')->constrained('proposals')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->text('loom_script')->nullable();
            $table->json('talking_points')->nullable();
            $table->text('internal_note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('proposal_notes');
    }
};
