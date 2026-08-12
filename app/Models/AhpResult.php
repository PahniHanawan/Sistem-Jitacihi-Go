<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AhpResult extends Model
{
    protected $table = 'ahp_results';
    protected $primaryKey = 'ahp_result_id';
    protected $fillable = ['period_id', 'criterion_id', 'weight', 'lambda_max', 'ci', 'cr', 'is_valid'];

    public function period()
    {
        return $this->belongsTo(AssessmentPeriod::class, 'period_id', 'period_id');
    }

    public function criterion()
    {
        return $this->belongsTo(Criterion::class, 'criterion_id', 'criterion_id');
    }
}
