<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PromotionDecision extends Model
{
    protected $table = 'promotion_decisions';
    protected $primaryKey = 'decision_id';
    protected $fillable = ['ranking_id', 'discount_type', 'reason', 'decided_by', 'decided_at'];

    public function rankingResult()
    {
        return $this->belongsTo(RankingResult::class, 'ranking_id', 'ranking_id');
    }

    public function decidedBy()
    {
        return $this->belongsTo(User::class, 'decided_by', 'user_id');
    }
}
