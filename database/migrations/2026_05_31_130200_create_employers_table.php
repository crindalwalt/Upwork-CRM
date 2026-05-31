<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employers', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('name', 255);
            $table->string('upwork_url', 512)->nullable()->unique();
            $table->string('location', 100)->nullable();
            $table->decimal('total_spent', 10, 2)->default(0);
            $table->decimal('hire_rate', 5, 2)->nullable();
            $table->unsignedInteger('reviews_count')->default(0);
            $table->boolean('payment_verified')->default(false);
            $table->unsignedInteger('open_jobs_count')->default(0);
            $table->date('member_since')->nullable();
            $table->text('internal_notes')->nullable();
            $table->string('flag')->nullable();
            $table->timestamps();

            $table->index('flag');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employers');
    }
};
