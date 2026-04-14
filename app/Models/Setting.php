<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    // Tentukan kolom mana saja yang boleh diisi
    protected $fillable = ['key', 'value'];
}