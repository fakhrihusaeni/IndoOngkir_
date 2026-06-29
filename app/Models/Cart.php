<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $fillable = ['user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(CartItem::class);
    }

    public function getTotalWeightAttribute(): int
    {
        return $this->items->sum(fn($item) => $item->product->weight * $item->quantity);
    }

    public function getSubtotalAttribute(): float
    {
        return $this->items->sum(fn($item) => $item->product->price * $item->quantity);
    }
}