<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Unit extends Model
{
    protected $fillable = ['code', 'name', 'status', 'description'];

    public function barangs(): HasMany
    {
        return $this->hasMany(Barang::class);
    }
}
