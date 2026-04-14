<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'category_id',
        'brand_id',
        'name',
        'description',
        'stock',
        'sku',
        'weight',
        'is_featured',
        'is_active',
    ];

    protected $casts = [
       
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'weight' => 'integer',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }
    
    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }


    // Helper: ambil harga termurah dari varian (untuk tampilan di katalog)
    public function getMinPriceAttribute()
    {
        return $this->variants->min('additional_price') ?? 0;
    }

    public function getMaxPriceAttribute()
    {
        return $this->variants->max('additional_price') ?? 0;
    }

   
}