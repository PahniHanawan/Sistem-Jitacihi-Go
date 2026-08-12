<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_assessments', function (Blueprint $table) {
            $table->id('assessment_id');
            $table->foreignId('product_id')->constrained('products', 'product_id')->onDelete('cascade');
            $table->foreignId('period_id')->constrained('assessment_periods', 'period_id')->onDelete('cascade');
            $table->integer('initial_stock');
            $table->integer('final_stock');
            $table->integer('units_sold');
            $table->decimal('selling_price', 12, 2);
            $table->decimal('cost_price', 12, 2);
            $table->date('entry_date');
            $table->enum('data_status', ['layak', 'belum memadai']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_assessments');
    }
};
