<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Criterion extends Model
{
    protected $table = 'criteria';
    protected $primaryKey = 'criterion_id';
    protected $fillable = ['criterion_code', 'criterion_name', 'type', 'status'];

    public function ahpResults()
    {
        return $this->hasMany(AhpResult::class, 'criterion_id', 'criterion_id');
    }

    public function normalizationResults()
    {
        return $this->hasMany(NormalizationResult::class, 'criterion_id', 'criterion_id');
    }
}
