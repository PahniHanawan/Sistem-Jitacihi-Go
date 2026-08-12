<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductAssessment extends Model
{
    protected $table = 'product_assessments';
    protected $primaryKey = 'assessment_id';
    protected $fillable = [
        'product_id', 'period_id', 'initial_stock', 'final_stock',
        'units_sold', 'selling_price', 'cost_price', 'entry_date', 'data_status'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }

    public function period()
    {
        return $this->belongsTo(AssessmentPeriod::class, 'period_id', 'period_id');
    }

    public function normalizationResults()
    {
        return $this->hasMany(NormalizationResult::class, 'assessment_id', 'assessment_id');
    }

    public function rankingResult()
    {
        return $this->hasOne(RankingResult::class, 'assessment_id', 'assessment_id');
    }
}
