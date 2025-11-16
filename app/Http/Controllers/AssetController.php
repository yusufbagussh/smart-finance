<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule; // <-- PENTING: Import Rule untuk validasi Enum

class AssetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Sebaiknya urutkan, misal berdasarkan nama
        $assets = Asset::orderBy('name')->get();
        return view('assets.index', compact('assets'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Cukup tampilkan view create
        return view('assets.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validasi lengkap untuk aset baru
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:assets,code', // Pastikan unik
            'issuer' => 'nullable|string|max:255',
            'asset_type' => [
                'required',
                Rule::in(['mutual_fund', 'gold']), // Validasi dari Enum
            ],
            'price_unit' => [
                'required',
                Rule::in(['unit', 'gram']), // Validasi dari Enum
            ],
            'current_price' => 'required|numeric|min:0'
        ]);

        Asset::create($validated);

        return redirect()->route('assets.index')
            ->with('success', 'Aset baru berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Kita tidak menggunakan halaman show individual untuk aset
        return redirect()->route('assets.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Asset $asset)
    {
        // Kode Anda sudah benar, menggunakan Route Model Binding
        return view('assets.edit', compact('asset'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Asset $asset)
    {
        // Validasi Anda sudah benar untuk alur edit
        // (kita tidak mengizinkan perubahan 'asset_type' atau 'price_unit' saat edit)
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:assets,code,' . $asset->id, // unique kecuali untuk diri sendiri
            'issuer' => 'nullable|string|max:255',
            'current_price' => 'required|numeric|min:0'
        ]);

        $asset->update($validated);

        return redirect()->route('assets.index')->with('success', 'Data aset berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Asset $asset)
    {
        // Tambahkan fungsi hapus
        try {
            $asset->delete();
            return redirect()->route('assets.index')->with('success', 'Aset berhasil dihapus.');
        } catch (\Illuminate\Database\QueryException $e) {
            // Tangani jika aset tidak bisa dihapus (karena terkait transaksi)
            return redirect()->route('assets.index')
                ->with('error', 'Aset tidak dapat dihapus karena sudah memiliki riwayat transaksi.');
        }
    }
}
