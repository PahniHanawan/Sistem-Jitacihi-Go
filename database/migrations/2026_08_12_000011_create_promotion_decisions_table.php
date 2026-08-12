<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('promotion_decisions', function (Blueprint $table) {
            $table->id('decision_id');
            $table->foreignId('ranking_id')->constrained('ranking_results', 'ranking_id')->onDelete('cascade');
            $table->string('discount_type', 50)->nullable();
            $table->text('reason')->nullable();
            $table->foreignId('decided_by')->constrained('users', 'user_id')->onDelete('cascade');
            $table->dateTime('decided_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('promotion_decisions');
    }
};
