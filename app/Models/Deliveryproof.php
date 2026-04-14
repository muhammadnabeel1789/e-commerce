<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryProof extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'courier_id',
        'type',
        'photo_path',
        'notes',
        'latitude',
        'longitude',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'latitude'   => 'float',
        'longitude'  => 'float',
    ];

    // ── Relasi ──

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function courier()
    {
        return $this->belongsTo(User::class, 'courier_id');
    }

    // ── Helpers ──

    /**
     * Label tipe foto yang ramah pengguna
     */
    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            'pick_up'   => 'Pengambilan Paket',
            'delivered' => 'Bukti Pengiriman',
            default     => ucfirst($this->type),
        };
    }

    /**
     * URL foto lengkap
     */
    public function getPhotoUrlAttribute(): string
    {
        return asset('storage/' . $this->photo_path);
    }
}