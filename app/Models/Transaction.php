<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'invoice_number', 'user_id', 'items', 'subtotal',
        'shipping_cost', 'total', 'recipient_name', 'recipient_address',
        'recipient_province', 'recipient_city', 'courier',
        'courier_service', 'status',
    ];

    protected $casts = [
        'items' => 'array',
        'subtotal' => 'float',
        'shipping_cost' => 'float',
        'total' => 'float',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'belum_bayar' => 'Belum Bayar',
            'dikirim'     => 'Dikirim',
            'selesai'     => 'Selesai',
            default       => '-',
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'belum_bayar' => 'yellow',
            'dikirim'     => 'blue',
            'selesai'     => 'green',
            default       => 'gray',
        };
    }

    public static function generateInvoiceNumber(): string
    {
        $prefix = 'INV-' . date('Ymd');
        $last = self::where('invoice_number', 'like', $prefix . '%')->latest()->first();
        $seq = $last ? (int) substr($last->invoice_number, -4) + 1 : 1;
        return $prefix . '-' . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }
}