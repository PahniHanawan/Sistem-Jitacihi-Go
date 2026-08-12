<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'products';
    protected $primaryKey = 'product_id';
    protected $fillable = ['product_code', 'product_name', 'category', 'status'];

    public function productAssessments()
    {
        return $this->hasMany(ProductAssessment::class, 'product_id', 'product_id');
    }
}
