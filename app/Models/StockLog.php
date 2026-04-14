<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'product_variant_id',
        'user_id',
        'quantity_change',
        'previous_stock',
        'current_stock',
        'type',
        'notes',
    ];

    // Relasi ke Produk Induk
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Relasi ke Varian (Ukuran/Warna spesifik)
    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}