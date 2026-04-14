<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    protected $fillable = [
        'product_id',
        'product_image_id',
        'size',
        'color',
        'color_code',
        'stock',
        'additional_price',
        'sku_variant',
    ];

    protected $casts = [
        'additional_price' => 'decimal:2',
        'stock' => 'integer',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function image()
    {
        return $this->belongsTo(ProductImage::class, 'product_image_id');
    }

    public function isInStock()
    {
        return $this->stock > 0;
    }

    public function getPriceAttribute()
    {
        return $this->additional_price;
    }
}