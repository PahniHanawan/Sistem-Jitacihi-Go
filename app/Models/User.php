<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $table = 'users';
    protected $primaryKey = 'user_id';
    protected $fillable = [
        'username',
        'email',
        'password',
        'role_id',
        'full_name',
        'status',
    ];
    protected $hidden = [
        'password',
        'remember_token',
    ];

    // ================================================================
    // 1. RELASI
    // ================================================================

    /**
     * Relasi ke tabel roles
     */
    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id', 'role_id');
    }

    /**
     * Relasi ke pairwise_comparisons (AHP yang dibuat oleh user)
     */
    public function pairwiseComparisons()
    {
        return $this->hasMany(PairwiseComparison::class, 'created_by', 'user_id');
    }

    /**
     * Relasi ke promotion_decisions (Keputusan promosi yang dibuat oleh user)
     */
    public function promotionDecisions()
    {
        return $this->hasMany(PromotionDecision::class, 'decided_by', 'user_id');
    }

    /**
     * Relasi ke reports (Laporan yang dibuat oleh user)
     */
    public function reports()
    {
        return $this->hasMany(Report::class, 'generated_by', 'user_id');
    }

    // ================================================================
    // 2. METHOD PENGECEKAN ROLE
    // ================================================================

    /**
     * Cek apakah user adalah Owner
     */
    public function isOwner(): bool
    {
        return optional($this->role)->role_name === 'owner';
    }

    /**
     * Cek apakah user adalah Admin
     */
    public function isAdmin(): bool
    {
        return optional($this->role)->role_name === 'admin';
    }

    /**
     * Cek apakah user aktif
     */
    public function isActive(): bool
    {
        return $this->status === 'aktif';
    }

    // ================================================================
    // 3. HELPER METHOD (Opsional, mempermudah penggunaan)
    // ================================================================

    /**
     * Mendapatkan nama role dalam format teks
     * Contoh: 'owner' → 'Owner', 'admin' → 'Admin'
     */
    public function getRoleNameAttribute(): string
    {
        return ucfirst(optional($this->role)->role_name ?? 'User');
    }

    /**
     * Mendapatkan inisial nama untuk avatar
     * Contoh: 'Intan Nurlatifah' → 'IN'
     */
    public function getInitialsAttribute(): string
    {
        $name = $this->full_name ?? $this->username ?? 'U';
        $parts = explode(' ', trim($name));
        $initials = '';

        foreach ($parts as $part) {
            if (!empty($part)) {
                $initials .= strtoupper($part[0]);
            }
        }

        return substr($initials, 0, 2) ?: 'U';
    }

    /**
     * Mendapatkan warna avatar berdasarkan role
     * Owner: indigo, Admin: slate
     */
    public function getAvatarColorAttribute(): string
    {
        if ($this->isOwner()) {
            return 'from-indigo-500 to-purple-600';
        }
        return 'from-slate-400 to-slate-600';
    }
}