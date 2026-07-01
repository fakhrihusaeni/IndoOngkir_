<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    // Tampilkan keranjang belanja
    public function index()
    {
        $cart = auth()->user()->cart;
        if ($cart) {
            $cart->load('items.product');
        }
        return view('cart.index', compact('cart'));
    }

    // Tambah produk ke keranjang
    public function add(Request $request, $productId)
    {
        // 1. Ambil data produk berdasarkan ID yang dikirim form
        $product = Product::findOrFail($productId);
        
        // 2. Ambil atau buat keranjang baru untuk user yang sedang login
        $cart = Cart::firstOrCreate(['user_id' => auth()->id()]);

        // 3. Cek apakah produk tersebut sudah ada di keranjang
        $cartItem = $cart->items()->where('product_id', $product->id)->first();

        if ($cartItem) {
            // Jika sudah ada, tambahkan quantity-nya
            $cartItem->increment('quantity');
        } else {
            // Jika belum ada, buat item baru di keranjang
            $cart->items()->create([
                'product_id' => $product->id,
                'quantity' => 1,
            ]);
        }

        // 4. Alihkan halaman ke halaman keranjang belanja dengan pesan sukses
        return redirect()->route('cart.index')->with('success', 'Produk berhasil ditambahkan ke keranjang!');
    }

    // Update quantity item
    public function update(Request $request, CartItem $cartItem)
    {
        $request->validate(['quantity' => 'required|integer|min:1']);

        if ($request->quantity > $cartItem->product->stock) {
            return back()->with('error', 'Stok tidak mencukupi!');
        }

        $cartItem->update(['quantity' => $request->quantity]);
        return back()->with('success', 'Keranjang diperbarui!');
    }

    // Hapus item dari keranjang
    public function remove(CartItem $cartItem)
    {
        $cartItem->delete();
        return back()->with('success', 'Produk dihapus dari keranjang!');
    }

    // Tampilan checkout dengan form pengiriman
    public function checkout(Request $request)
    {
        $cart = auth()->user()->cart;
        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Keranjang kosong!');
        }

        $cart->load('items.product');

        // Filter hanya item yang dipilih
        if ($request->has('items')) {
            $selectedItems = $cart->items->whereIn('id', $request->items)->values();
        } else {
            $selectedItems = $cart->items;
        }

        if ($selectedItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Pilih minimal 1 produk!');
        }

        return view('cart.checkout', compact('cart', 'selectedItems'));
    }
}