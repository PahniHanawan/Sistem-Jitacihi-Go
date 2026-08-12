<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PairwiseComparison extends Model
{
    protected $table = 'pairwise_comparisons';
    protected $primaryKey = 'comparison_id';
    protected $fillable = ['period_id', 'criterion_a_id', 'criterion_b_id', 'value', 'created_by'];

    public function period()
    {
        return $this->belongsTo(AssessmentPeriod::class, 'period_id', 'period_id');
    }

    public function criterionA()
    {
        return $this->belongsTo(Criterion::class, 'criterion_a_id', 'criterion_id');
    }

    public function criterionB()
    {
        return $this->belongsTo(Criterion::class, 'criterion_b_id', 'criterion_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by', 'user_id');
    }
}
