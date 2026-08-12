<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ahp_results', function (Blueprint $table) {
            $table->id('ahp_result_id');
            $table->foreignId('period_id')->constrained('assessment_periods', 'period_id')->onDelete('cascade');
            $table->foreignId('criterion_id')->constrained('criteria', 'criterion_id')->onDelete('cascade');
            $table->decimal('weight', 6, 4);
            $table->decimal('lambda_max', 8, 4);
            $table->decimal('ci', 8, 4);
            $table->decimal('cr', 8, 4);
            $table->boolean('is_valid');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ahp_results');
    }
};
