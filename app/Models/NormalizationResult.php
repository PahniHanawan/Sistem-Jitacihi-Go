<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NormalizationResult extends Model
{
    protected $table = 'normalization_results';
    protected $primaryKey = 'norm_id';
    protected $fillable = ['assessment_id', 'criterion_id', 'raw_value', 'normalized_value'];

    public function productAssessment()
    {
        return $this->belongsTo(ProductAssessment::class, 'assessment_id', 'assessment_id');
    }

    public function criterion()
    {
        return $this->belongsTo(Criterion::class, 'criterion_id', 'criterion_id');
    }
}
