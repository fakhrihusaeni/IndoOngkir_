<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 13px; color: #333; }
        .header { background: #f97316; color: white; padding: 20px 30px; }
        .header h1 { font-size: 24px; font-weight: bold; }
        .header p { font-size: 12px; opacity: 0.9; }
        .content { padding: 24px 30px; }
        .invoice-info { display: flex; justify-content: space-between; margin-bottom: 20px; }
        .badge { display: inline-block; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: bold; }
        .badge-yellow { background: #fef9c3; color: #854d0e; }
        .badge-blue { background: #dbeafe; color: #1e40af; }
        .badge-green { background: #dcfce7; color: #166534; }
        .section-title { font-weight: bold; margin-bottom: 8px; border-bottom: 2px solid #f97316; padding-bottom: 4px; }
        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #fff7ed; text-align: left; padding: 8px 10px; font-size: 12px; border: 1px solid #fed7aa; }
        td { padding: 8px 10px; border: 1px solid #e5e7eb; font-size: 12px; }
        .text-right { text-align: right; }
        .total-row td { font-weight: bold; background: #fff7ed; }
        .footer { margin-top: 30px; text-align: center; color: #9ca3af; font-size: 11px; border-top: 1px solid #e5e7eb; padding-top: 12px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>🛍️ IndoOngkir</h1>
        <p>Toko Online UMKM | Pengiriman ke Seluruh Indonesia</p>
    </div>

    <div class="content">
        <div class="invoice-info">
            <div>
                <h2 style="font-size:18px; font-weight:bold; color:#f97316;">INVOICE</h2>
                <p style="color:#6b7280;">{{ $transaction->invoice_number }}</p>
                <p style="color:#6b7280; font-size:12px;">{{ $transaction->created_at->format('d F Y, H:i') }} WIB</p>
            </div>
            <div>
                @php
                    $badgeClass = match($transaction->status) {
                        'belum_bayar' => 'badge-yellow',
                        'dikirim' => 'badge-blue',
                        'selesai' => 'badge-green',
                        default => ''
                    };
                @endphp
                <span class="badge {{ $badgeClass }}">{{ $transaction->status_label }}</span>
            </div>
        </div>

        <div class="grid-2">
            <div>
                <p class="section-title">Informasi Pembeli</p>
                <p><strong>{{ $transaction->user->name }}</strong></p>
                <p>{{ $transaction->user->email }}</p>
            </div>
            <div>
                <p class="section-title">Alamat Pengiriman</p>
                <p><strong>{{ $transaction->recipient_name }}</strong></p>
                <p>{{ $transaction->recipient_address }}</p>
                <p>{{ $transaction->recipient_city }}, {{ $transaction->recipient_province }}</p>
                <p style="margin-top:4px; color:#f97316;"><strong>{{ $transaction->courier }} - {{ $transaction->courier_service }}</strong></p>
            </div>
        </div>

        <p class="section-title">Detail Produk</p>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Produk</th>
                    <th class="text-right">Harga</th>
                    <th class="text-right">Qty</th>
                    <th class="text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transaction->items as $i => $item)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $item['product_name'] }}</td>
                    <td class="text-right">Rp {{ number_format($item['price'], 0, ',', '.') }}</td>
                    <td class="text-right">{{ $item['quantity'] }}</td>
                    <td class="text-right">Rp {{ number_format($item['subtotal'], 0, ',', '.') }}</td>
                </tr>
                @endforeach
                <tr>
                    <td colspan="4" class="text-right">Subtotal</td>
                    <td class="text-right">Rp {{ number_format($transaction->subtotal, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td colspan="4" class="text-right">Ongkos Kirim ({{ $transaction->courier }} {{ $transaction->courier_service }})</td>
                    <td class="text-right">Rp {{ number_format($transaction->shipping_cost, 0, ',', '.') }}</td>
                </tr>
                <tr class="total-row">
                    <td colspan="4" class="text-right" style="color:#f97316;">TOTAL</td>
                    <td class="text-right" style="color:#f97316;">Rp {{ number_format($transaction->total, 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>

        <div class="footer">
            <p>Terima kasih telah berbelanja di IndoOngkir!</p>
            <p>Dicetak pada: {{ now()->format('d F Y, H:i') }} WIB</p>
        </div>
    </div>
</body>
</html>