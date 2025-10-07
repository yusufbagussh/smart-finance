@extends('layouts.app')
@section('title', 'Profil Pengguna')
@section('content') <div class="max-w-2xl bg-white rounded-2xl shadow p-6">
        <h1 class="text-xl font-semibold mb-4">Profil Pengguna</h1>
        <div class="grid sm:grid-cols-2 gap-4">
            <div>
                <label class="text-sm">Nama</label>
                <input class="mt-1 w-full rounded-lg border-slate-300" value="Budi Setiawan">
            </div>
            <div>
                <label class="text-sm">Email</label>
                <input class="mt-1 w-full rounded-lg border-slate-300" value="budi@mail.com">
            </div>
            <div>
                <label class="text-sm">Role</label>
                <input class="mt-1 w-full rounded-lg border-slate-300" value="user" disabled>
            </div>
            <div class="flex items-end">
                <button class="w-full px-4 py-2 rounded-lg bg-emerald-600 text-white hover:bg-emerald-700">Simpan
                    Perubahan</button>
            </div>
        </div>

        <hr class="my-6">
        <button class="px-4 py-2 rounded-lg bg-slate-100 hover:bg-slate-200">Ganti Password</button>
        <button class="px-4 py-2 rounded-lg bg-rose-600 text-white hover:bg-rose-700 ml-2">Logout</button>
    </div>
@endSection
