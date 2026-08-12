<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $table = 'users';
    protected $primaryKey = 'user_id';
    protected $fillable = ['username', 'password', 'role_id', 'full_name', 'status', 'email'];
    protected $hidden = ['password', 'remember_token'];

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id', 'role_id');
    }

    public function pairwiseComparisons()
    {
        return $this->hasMany(PairwiseComparison::class, 'created_by', 'user_id');
    }

    public function promotionDecisions()
    {
        return $this->hasMany(PromotionDecision::class, 'decided_by', 'user_id');
    }

    public function reports()
    {
        return $this->hasMany(Report::class, 'generated_by', 'user_id');
    }

    public function isOwner(): bool
    {
        return optional($this->role)->role_name === 'owner';
    }

    public function isAdmin(): bool
    {
        return optional($this->role)->role_name === 'admin';
    }
}
