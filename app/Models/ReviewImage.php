<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReviewImage extends Model
{
    // Mengizinkan Laravel untuk menyimpan data ke kolom ini
    protected $fillable = [
        'review_id',
        'image_path',
    ];

    // Relasi balik ke model Review
    public function review()
    {
        return $this->belongsTo(Review::class);
    }
}