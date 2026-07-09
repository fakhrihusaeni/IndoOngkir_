<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class TransactionController extends Controller
{
    // Proses checkout → buat transaksi
    public function store(Request $request)
    {
        $request->validate([
            'recipient_name'    => 'required|string|max:255',
            'recipient_address' => 'required|string',
            'province_name'     => 'required|string',
            // 'city_id' DIHAPUS dari validasi karena tidak dikirim dari form dan database hanya butuh city_name
            'city_name'         => 'required|string', 
            'courier'           => 'required|in:jne,pos,tiki',
            'courier_service'   => 'required|string',
            'shipping_cost'     => 'required|numeric|min:0',
        ]);

        $cart = auth()->user()->cart;
        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Keranjang kosong!');
        }

        $cart->load('items.product');

        // Filter hanya item yang dipilih (kalau ada selected_items dari checkbox)
        $itemsToProcess = $cart->items;
        if ($request->has('selected_items')) {
            $itemsToProcess = $cart->items->whereIn('id', $request->selected_items);
        }

        if ($itemsToProcess->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Pilih minimal 1 produk!');
        }

        // Snapshot item produk saat checkout
        $items = $itemsToProcess->map(fn($item) => [
            'product_id'   => $item->product->id,
            'product_name' => $item->product->name,
            'price'        => $item->product->price,
            'quantity'     => $item->quantity,
            'weight'       => $item->product->weight,
            'subtotal'     => $item->product->price * $item->quantity,
        ])->toArray();

        $subtotal = collect($items)->sum('subtotal');
        $total    = $subtotal + $request->shipping_cost;

        DB::transaction(function () use ($request, $items, $subtotal, $total, $itemsToProcess) {
            // Buat transaksi
            Transaction::create([
                'invoice_number'     => Transaction::generateInvoiceNumber(),
                'user_id'            => auth()->id(),
                'items'              => $items,
                'subtotal'           => $subtotal,
                'shipping_cost'      => $request->shipping_cost,
                'total'              => $total,
                'recipient_name'     => $request->recipient_name,
                'recipient_address'  => $request->recipient_address,
                'recipient_province' => $request->province_name,
                'recipient_city'     => $request->city_name,
                'courier'            => strtoupper($request->courier),
                'courier_service'    => $request->courier_service,
                'status'             => 'belum_bayar',
            ]);

            // Kurangi stok produk & hapus item yang sudah di-checkout
            foreach ($itemsToProcess as $item) {
                $item->product->decrement('stock', $item->quantity);
                $item->delete();
            }
    });

        return redirect()->route('transactions.index')->with('success', 'Pesanan berhasil dibuat!');
    }

    // Daftar transaksi pembeli
    public function index()
    {
        $transactions = auth()->user()->transactions()->latest()->paginate(10);
        return view('transactions.index', compact('transactions'));
    }

    // Detail transaksi
    public function show(Transaction $transaction)
    {
        if ($transaction->user_id !== auth()->id() && !auth()->user()->isAdmin()) {
            abort(403);
        }
        return view('transactions.show', compact('transaction'));
    }

    // Admin: Daftar semua transaksi
    public function adminIndex()
    {
        $transactions = Transaction::with('user')->latest()->paginate(15);
        return view('admin.transactions.index', compact('transactions'));
    }

    // Admin: Update status transaksi
    public function updateStatus(Request $request, Transaction $transaction)
    {
        $request->validate([
            'status' => 'required|in:belum_bayar,dikirim,selesai',
        ]);

        $transaction->update(['status' => $request->status]);
        return back()->with('success', 'Status transaksi diperbarui!');
    }

    // Cetak invoice PDF
    public function printInvoice(Transaction $transaction)
    {
        if ($transaction->user_id !== auth()->id() && !auth()->user()->isAdmin()) {
            abort(403);
        }

        $pdf = Pdf::loadView('transactions.invoice', compact('transaction'));
        $pdf->setPaper('A4', 'portrait');

        return $pdf->stream("Invoice-{$transaction->invoice_number}.pdf");
    }
}