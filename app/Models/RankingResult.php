<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RankingResult extends Model
{
    protected $table = 'ranking_results';
    protected $primaryKey = 'ranking_id';
    protected $fillable = ['assessment_id', 'period_id', 'preference_value', 'rank'];

    public function productAssessment()
    {
        return $this->belongsTo(ProductAssessment::class, 'assessment_id', 'assessment_id');
    }

    public function period()
    {
        return $this->belongsTo(AssessmentPeriod::class, 'period_id', 'period_id');
    }

    public function promotionDecision()
    {
        return $this->hasOne(PromotionDecision::class, 'ranking_id', 'ranking_id');
    }
}
