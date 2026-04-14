<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'courier_id',
        'order_number',
        'status',
        'subtotal',
        'discount_amount',
        'shipping_cost',
        'total',
        'payment_method',
        'payment_status',
        'notes',
        'recipient_name',
        'recipient_phone',
        'shipping_address',
        'province',
        'city',
        'district',
        'village',
        'postal_code',
        'courier_name',
        'tracking_number',
        'shipped_at',
        'delivered_at',
        'cancelled_at',
        'courier_task_status',
        'picked_up_at',
        'shipping_eta',
        'shipping_distance',
        'snap_token',
        'cancel_request_status',
        'cancel_reason',
        'cancel_reject_reason'
    ];

    protected $casts = [
        'subtotal'           => 'decimal:2',
        'discount_amount'    => 'decimal:2',
        'shipping_cost'      => 'decimal:2',
        'total'              => 'decimal:2',
        'shipped_at'         => 'datetime',
        'delivered_at'       => 'datetime',
        'cancelled_at'       => 'datetime',
        'cancel_requested_at'=> 'datetime',
        'picked_up_at'       => 'datetime',
        'shipping_distance'  => 'decimal:2',
    ];

    // ── Relasi existing ──

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    // ── Relasi kurir (BARU) ──

    public function courier()
    {
        return $this->belongsTo(User::class, 'courier_id');
    }

    public function deliveryProofs()
    {
        return $this->hasMany(DeliveryProof::class);
    }

    public function pickUpProof()
    {
        return $this->hasOne(DeliveryProof::class)
                    ->where('type', 'pick_up')
                    ->latest();
    }

    public function deliveredProof()
    {
        return $this->hasOne(DeliveryProof::class)
                    ->where('type', 'delivered')
                    ->latest();
    }

    // ── Helper kurir (BARU) ──

    public function getIsAssignedToCourierAttribute(): bool
    {
        return !is_null($this->courier_id);
    }

    public function getCourierTaskLabelAttribute(): string
    {
        return match($this->courier_task_status) {
            'assigned'  => '📋 Ditugaskan',
            'picked_up' => '📦 Paket Diambil',
            'delivered' => '✅ Terkirim',
            default     => '—',
        };
    }

    // ── Generate nomor order ──

    public static function generateOrderNumber()
    {
        do {
            $orderNumber = 'INV' . date('Ymd') . rand(100000, 999999);
        } while (self::where('order_number', $orderNumber)->exists());

        return $orderNumber;
    }
}