<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('normalization_results', function (Blueprint $table) {
            $table->id('norm_id');
            $table->foreignId('assessment_id')->constrained('product_assessments', 'assessment_id')->onDelete('cascade');
            $table->foreignId('criterion_id')->constrained('criteria', 'criterion_id')->onDelete('cascade');
            $table->decimal('raw_value', 12, 4);
            $table->decimal('normalized_value', 8, 4);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('normalization_results');
    }
};
