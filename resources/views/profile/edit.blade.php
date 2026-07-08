@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto py-8">
    <h2 class="text-2xl font-bold text-slate-800 mb-6">Pengaturan Profil</h2>

    <form action="{{ route('profile.update') }}" method="POST" class="space-y-8">
        @csrf
        @method('PATCH')

        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
            <h3 class="text-lg font-semibold text-slate-700 mb-4 border-b pb-2">Informasi Akun</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-600">Nama Lengkap</label>
                    <input type="text" name="name" value="{{ $user->name }}" class="w-full mt-1 p-2 border rounded-lg focus:ring-amber-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-600">Alamat Email</label>
                    <input type="email" name="email" value="{{ $user->email }}" class="w-full mt-1 p-2 border rounded-lg focus:ring-amber-500">
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
            <h3 class="text-lg font-semibold text-slate-700 mb-4 border-b pb-2">Keamanan</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-600">Password Baru</label>
                    <input type="password" name="password" placeholder="Kosongkan jika tidak ingin mengubah" class="w-full mt-1 p-2 border rounded-lg focus:ring-amber-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-600">Konfirmasi Password</label>
                    <input type="password" name="password_confirmation" class="w-full mt-1 p-2 border rounded-lg focus:ring-amber-500">
                </div>
            </div>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="bg-amber-500 hover:bg-amber-600 text-white font-bold py-2 px-6 rounded-xl transition">
                Simpan Semua Perubahan
            </button>
        </div>
    </form>
</div>
@endsection