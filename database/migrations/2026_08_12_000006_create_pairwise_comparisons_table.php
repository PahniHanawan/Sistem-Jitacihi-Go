<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pairwise_comparisons', function (Blueprint $table) {
            $table->id('comparison_id');
            $table->foreignId('period_id')->constrained('assessment_periods', 'period_id')->onDelete('cascade');
            $table->foreignId('criterion_a_id')->constrained('criteria', 'criterion_id')->onDelete('cascade');
            $table->foreignId('criterion_b_id')->constrained('criteria', 'criterion_id')->onDelete('cascade');
            $table->decimal('value', 5, 4);
            $table->foreignId('created_by')->constrained('users', 'user_id')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pairwise_comparisons');
    }
};
