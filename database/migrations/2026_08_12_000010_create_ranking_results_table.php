<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ranking_results', function (Blueprint $table) {
            $table->id('ranking_id');
            $table->foreignId('assessment_id')->constrained('product_assessments', 'assessment_id')->onDelete('cascade');
            $table->foreignId('period_id')->constrained('assessment_periods', 'period_id')->onDelete('cascade');
            $table->decimal('preference_value', 8, 4);
            $table->integer('rank');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ranking_results');
    }
};
