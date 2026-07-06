<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    private string $apiKey;
    private string $baseUrl;

    public function __construct()
    {
        $this->apiKey  = config('services.komerce.api_key');
        $this->baseUrl = config('services.komerce.base_url', 'https://api.komerce.id');
    }

    // Tampilkan halaman pilih metode bayar
    public function show(Transaction $transaction)
    {
        if ($transaction->user_id !== auth()->id()) {
            abort(403);
        }

        if ($transaction->payment_status === 'paid') {
            return redirect()->route('transactions.show', $transaction)
                ->with('info', 'Transaksi ini sudah dibayar.');
        }

        // Metode pembayaran yang tersedia
        $paymentMethods = [
            'transfer' => [
                'label' => 'Transfer Bank',
                'icon'  => '🏦',
                'options' => [
                    ['code' => 'bca_transfer', 'name' => 'BCA Transfer'],
                    ['code' => 'bni_transfer', 'name' => 'BNI Transfer'],
                    ['code' => 'bri_transfer', 'name' => 'BRI Transfer'],
                    ['code' => 'mandiri_transfer', 'name' => 'Mandiri Transfer'],
                ]
            ],
            'ewallet' => [
                'label' => 'E-Wallet',
                'icon'  => '📱',
                'options' => [
                    ['code' => 'gopay', 'name' => 'GoPay'],
                    ['code' => 'ovo', 'name' => 'OVO'],
                    ['code' => 'dana', 'name' => 'DANA'],
                    ['code' => 'shopeepay', 'name' => 'ShopeePay'],
                ]
            ],
            'qris' => [
                'label' => 'QRIS',
                'icon'  => '⚡',
                'options' => [
                    ['code' => 'qris', 'name' => 'QRIS (Semua E-Wallet)'],
                ]
            ],
        ];

        return view('transactions.payment', compact('transaction', 'paymentMethods'));
    }

    // Proses pembayaran ke Komerce API
    public function process(Request $request, Transaction $transaction)
    {
        if ($transaction->user_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'payment_method' => 'required|string',
        ]);

        try {
            // Kirim request ke Komerce Payment API
            $response = Http::timeout(15)
                ->withHeaders([
                    'key'          => $this->apiKey,
                    'Content-Type' => 'application/json',
                ])
                ->post("{$this->baseUrl}/payment/v1/payment-link/create", [
                    'order_id'     => $transaction->invoice_number,
                    'amount'       => (int) $transaction->total,
                    'payment_type' => $request->payment_method,
                    'customer_name'  => $transaction->recipient_name,
                    'customer_email' => $transaction->user->email,
                    'customer_phone' => '08000000000',
                    'item_details'   => collect($transaction->items)->map(fn($item) => [
                        'item_id'    => $item['product_id'],
                        'price'      => (int) $item['price'],
                        'quantity'   => $item['quantity'],
                        'item_name'  => $item['product_name'],
                    ])->toArray(),
                    'callback_url' => route('payment.callback'),
                    'redirect_url' => route('transactions.show', $transaction),
                ]);

            if ($response->successful()) {
                $data = $response->json();

                // Update transaksi dengan info payment
                $transaction->update([
                    'payment_method' => $request->payment_method,
                    'payment_status' => 'pending',
                    'payment_token'  => $data['data']['token'] ?? null,
                    'payment_url'    => $data['data']['payment_url'] ?? $data['data']['redirect_url'] ?? null,
                    'payment_ref'    => $data['data']['order_id'] ?? $transaction->invoice_number,
                ]);

                // Redirect ke payment URL Komerce
                if (!empty($transaction->payment_url)) {
                    return redirect($transaction->payment_url);
                }

                return redirect()->route('transactions.show', $transaction)
                    ->with('success', 'Pembayaran sedang diproses!');
            }

            // Log error response
            Log::error('Komerce Payment Error', [
                'status'   => $response->status(),
                'response' => $response->json(),
            ]);

            return back()->with('error', 'Gagal memproses pembayaran: ' . ($response->json()['message'] ?? 'Coba lagi'));

        } catch (\Exception $e) {
            Log::error('Payment Exception: ' . $e->getMessage());
            return back()->with('error', 'Koneksi ke payment gateway gagal. Coba lagi.');
        }
    }

    // Callback dari Komerce (webhook)
    public function callback(Request $request)
    {
        Log::info('Payment Callback:', $request->all());

        $orderId       = $request->input('order_id');
        $paymentStatus = $request->input('payment_status');
        $paymentRef    = $request->input('payment_ref');

        $transaction = Transaction::where('invoice_number', $orderId)->first();

        if (!$transaction) {
            return response()->json(['message' => 'Transaction not found'], 404);
        }

        // Update status berdasarkan callback
        $newPaymentStatus = match($paymentStatus) {
            'settlement', 'capture', 'paid', 'success' => 'paid',
            'pending'                                    => 'pending',
            'deny', 'cancel', 'failure', 'failed'       => 'failed',
            'expire', 'expired'                          => 'expired',
            default                                      => $transaction->payment_status,
        };

        $transaction->update([
            'payment_status' => $newPaymentStatus,
            'payment_ref'    => $paymentRef ?? $transaction->payment_ref,
            'paid_at'        => $newPaymentStatus === 'paid' ? now() : null,
            // Jika sudah bayar, ubah status transaksi ke "dikirim"
            'status'         => $newPaymentStatus === 'paid' ? 'dikirim' : $transaction->status,
        ]);

        return response()->json(['message' => 'OK']);
    }

    // Cek status pembayaran manual
    public function checkStatus(Transaction $transaction)
    {
        if ($transaction->user_id !== auth()->id() && !auth()->user()->isAdmin()) {
            abort(403);
        }

        try {
            $response = Http::timeout(10)
                ->withHeaders(['key' => $this->apiKey])
                ->get("{$this->baseUrl}/payment/v1/payment-link/status/{$transaction->invoice_number}");

            if ($response->successful()) {
                $data          = $response->json();
                $paymentStatus = $data['data']['payment_status'] ?? null;

                if ($paymentStatus) {
                    $newStatus = match($paymentStatus) {
                        'settlement', 'capture', 'paid', 'success' => 'paid',
                        'pending'                                    => 'pending',
                        'deny', 'cancel', 'failure', 'failed'       => 'failed',
                        'expire', 'expired'                          => 'expired',
                        default                                      => $transaction->payment_status,
                    };

                    $transaction->update([
                        'payment_status' => $newStatus,
                        'paid_at'        => $newStatus === 'paid' ? now() : null,
                        'status'         => $newStatus === 'paid' ? 'dikirim' : $transaction->status,
                    ]);
                }
            }
        } catch (\Exception $e) {
            Log::error('Check Status Error: ' . $e->getMessage());
        }

        return redirect()->route('transactions.show', $transaction)
            ->with('success', 'Status pembayaran diperbarui!');
    }
}