<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssessmentPeriod extends Model
{
    protected $table = 'assessment_periods';
    protected $primaryKey = 'period_id';
    protected $fillable = ['period_name', 'start_date', 'end_date', 'status'];

    public function pairwiseComparisons()
    {
        return $this->hasMany(PairwiseComparison::class, 'period_id', 'period_id');
    }

    public function ahpResults()
    {
        return $this->hasMany(AhpResult::class, 'period_id', 'period_id');
    }

    public function productAssessments()
    {
        return $this->hasMany(ProductAssessment::class, 'period_id', 'period_id');
    }

    public function rankingResults()
    {
        return $this->hasMany(RankingResult::class, 'period_id', 'period_id');
    }
}
