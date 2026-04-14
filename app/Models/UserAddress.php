<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserAddress extends Model
{
    protected $fillable = [
        'user_id',
        'label',
        'recipient_name',
        'phone',
        'address',
        'province',
        'city',
        'district',
        'village', 
        'postal_code',
        'is_default',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($address) {
            if (!UserAddress::where('user_id', $address->user_id)->exists()) {
                $address->is_default = true;
            }
        });

        static::saved(function ($address) {
            if ($address->is_default) {
                UserAddress::where('user_id', $address->user_id)
                    ->where('id', '!=', $address->id)
                    ->update(['is_default' => false]);
                
                // Update nomor HP user agar sinkron dengan alamat utama (default)
                $address->user()->update(['phone' => $address->phone]);
            }
        });
    }
}