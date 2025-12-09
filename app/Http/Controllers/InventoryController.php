<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Kategori;
use App\Models\Satuan;
use App\Models\Gudang;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        
        // Query dasar dengan relasi
        $query = Barang::with('kategori', 'satuan', 'gudang');
        
        // Filter berdasarkan pencarian
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_barang', 'like', '%' . $search . '%')
                  ->orWhere('kode_barang', 'like', '%' . $search . '%')
                  ->orWhereHas('kategori', function($query) use ($search) {
                      $query->where('nama_kategori', 'like', '%' . $search . '%');
                  });
            });
        }
        
        // Filter berdasarkan kategori
        if ($request->has('category') && !empty($request->category)) {
            $query->where('kategori_id', $request->category);
        }
        
        // Filter berdasarkan status stok - LOGIKA DIPERBAIKI
        if ($request->has('status') && !empty($request->status)) {
            switch ($request->status) {
                case 'out':
                    // Stok habis = stok = 0
                    $query->where('stok', '<=', 0);
                    break;
                case 'critical':
                    // Stok kritis = stok <= stok_minimal DAN stok > 0
                    $query->where('stok', '>', 0)
                          ->whereRaw('stok <= stok_minimal');
                    break;
                case 'low':
                    // Stok rendah = stok > stok_minimal DAN stok <= (stok_minimal * 2)
                    $query->whereRaw('stok > stok_minimal')
                          ->whereRaw('stok <= (stok_minimal * 2)');
                    break;
                case 'good':
                    // Stok baik = stok > (stok_minimal * 2)
                    $query->whereRaw('stok > (stok_minimal * 2)');
                    break;
            }
        }
        
        // Sorting dan pagination
        $items = $query->latest()->paginate(10)->withQueryString();
        
        // Ambil data untuk filter dropdown
        $categories = Kategori::all();
        $units = Satuan::all();
        $warehouses = Gudang::all();
        
        // Hitung stats dengan kategori yang jelas
        $stats = [
            'total_items' => Barang::count(),
            'total_categories' => Kategori::count(),
            // Stok kritis: stok > 0 dan stok <= stok_minimal
            'critical_stock' => Barang::where('stok', '>', 0)
                                  ->whereRaw('stok <= stok_minimal')
                                  ->count(),
            // Stok rendah: stok > stok_minimal dan stok <= (stok_minimal * 2)
            'low_stock' => Barang::whereRaw('stok > stok_minimal')
                                ->whereRaw('stok <= (stok_minimal * 2)')
                                ->count(),
            'out_of_stock' => Barang::where('stok', '<=', 0)->count(),
        ];
        
        // Jika ada filter aktif, hitung ulang stats untuk data yang difilter
        if ($request->has('search') || $request->has('category') || $request->has('status')) {
            $filteredQuery = Barang::query();
            
            // Terapkan filter yang sama
            if ($request->has('search') && !empty($request->search)) {
                $search = $request->search;
                $filteredQuery->where(function($q) use ($search) {
                    $q->where('nama_barang', 'like', '%' . $search . '%')
                      ->orWhere('kode_barang', 'like', '%' . $search . '%')
                      ->orWhereHas('kategori', function($query) use ($search) {
                          $query->where('nama_kategori', 'like', '%' . $search . '%');
                      });
                });
            }
            
            if ($request->has('category') && !empty($request->category)) {
                $filteredQuery->where('kategori_id', $request->category);
            }
            
            if ($request->has('status') && !empty($request->status)) {
                switch ($request->status) {
                    case 'out':
                        $filteredQuery->where('stok', '<=', 0);
                        break;
                    case 'critical':
                        $filteredQuery->where('stok', '>', 0)
                                      ->whereRaw('stok <= stok_minimal');
                        break;
                    case 'low':
                        $filteredQuery->whereRaw('stok > stok_minimal')
                                      ->whereRaw('stok <= (stok_minimal * 2)');
                        break;
                    case 'good':
                        $filteredQuery->whereRaw('stok > (stok_minimal * 2)');
                        break;
                }
            }
            
            $filteredItems = $filteredQuery->get();
            
            $stats['filtered_total'] = $filteredItems->count();
            $stats['filtered_critical_stock'] = $filteredItems->filter(function($item) {
                return $item->stok > 0 && $item->stok <= $item->stok_minimal;
            })->count();
            $stats['filtered_low_stock'] = $filteredItems->filter(function($item) {
                return $item->stok > $item->stok_minimal && $item->stok <= ($item->stok_minimal * 2);
            })->count();
            $stats['filtered_out_of_stock'] = $filteredItems->filter(function($item) {
                return $item->stok <= 0;
            })->count();
        }
        
        return view('admin.inventory', compact(
            'user', 
            'items', 
            'categories', 
            'units', 
            'warehouses', 
            'stats'
        ));
    }
    
    public function create()
    {
        $categories = Kategori::all();
        $units = Satuan::all();
        $warehouses = Gudang::all();
        return view('admin.inventory.create', compact('categories', 'units', 'warehouses'));
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'kode_barang' => 'required|unique:barangs,kode_barang',
            'nama_barang' => 'required',
            'kategori_id' => 'required|exists:kategoris,id',
            'satuan_id' => 'required|exists:satuans,id',
            'gudang_id' => 'nullable|exists:gudangs,id',
            'stok' => 'required|integer|min:0',
            'stok_minimal' => 'required|integer|min:1',
            'harga_beli' => 'nullable|numeric|min:0',
            'harga_jual' => 'nullable|numeric|min:0',
            'lokasi' => 'nullable',
            'keterangan' => 'nullable',
            // Hapus validasi untuk gambar karena fitur gambar sudah dihapus
        ]);
        
        // Hapus field gambar karena sudah tidak ada
        $data = $request->all();
        
        Barang::create($data);
        
        return redirect()->route('admin.inventory')
            ->with('success', 'Barang berhasil ditambahkan.');
    }
    
    public function edit(Barang $barang)
    {
        $categories = Kategori::all();
        $units = Satuan::all();
        $warehouses = Gudang::all();
        
        return response()->json([
            'barang' => $barang->load('kategori', 'satuan', 'gudang'),
            'categories' => $categories,
            'units' => $units,
            'warehouses' => $warehouses
        ]);
    }
    
    public function update(Request $request, Barang $barang)
    {
        $request->validate([
            'kode_barang' => 'required|unique:barangs,kode_barang,' . $barang->id,
            'nama_barang' => 'required',
            'kategori_id' => 'required|exists:kategoris,id',
            'satuan_id' => 'required|exists:satuans,id',
            'gudang_id' => 'nullable|exists:gudangs,id',
            'stok' => 'required|integer|min:0',
            'stok_minimal' => 'required|integer|min:1',
            'harga_beli' => 'nullable|numeric|min:0',
            'harga_jual' => 'nullable|numeric|min:0',
            'lokasi' => 'nullable',
            'keterangan' => 'nullable',
            // Hapus validasi untuk gambar karena fitur gambar sudah dihapus
        ]);
        
        // Hapus field gambar karena sudah tidak ada
        $data = $request->all();
        
        $barang->update($data);
        
        return redirect()->route('admin.inventory')
            ->with('success', 'Barang berhasil diperbarui.');
    }
    
    public function destroy(Barang $barang)
    {
        // Hapus logika delete image karena fitur gambar sudah dihapus
        $barang->delete();
        
        return redirect()->route('admin.inventory')
            ->with('success', 'Barang berhasil dihapus.');
    }
    
    public function show(Barang $barang)
    {
        return response()->json([
            'barang' => $barang->load('kategori', 'satuan', 'gudang')
        ]);
    }
    
    public function restock(Request $request, Barang $barang)
    {
        $request->validate([
            'jumlah' => 'required|integer|min:1',
            'harga_beli' => 'nullable|numeric|min:0',
            'keterangan' => 'nullable',
        ]);
        
        $barang->increment('stok', $request->jumlah);
        
        // Update harga beli if provided
        if ($request->filled('harga_beli')) {
            $barang->update(['harga_beli' => $request->harga_beli]);
        }
        
        return redirect()->route('admin.inventory')
            ->with('success', 'Stok berhasil ditambahkan.');
    }
    
    public function getBarangByKode($kode)
    {
        $barang = Barang::with('kategori', 'satuan', 'gudang')
            ->where('kode_barang', $kode)
            ->first();
            
        if (!$barang) {
            return response()->json([
                'error' => 'Barang tidak ditemukan'
            ], 404);
        }
        
        return response()->json([
            'barang' => $barang
        ]);
    }
    
    public function search(Request $request)
    {
        $search = $request->input('search');
        
        $items = Barang::with('kategori', 'satuan', 'gudang')
            ->where('kode_barang', 'like', "%{$search}%")
            ->orWhere('nama_barang', 'like', "%{$search}%")
            ->orWhereHas('kategori', function($query) use ($search) {
                $query->where('nama_kategori', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(10);
            
        return view('admin.inventory', compact('items'));
    }
    
    /**
     * Menyimpan kategori baru via AJAX (untuk modal quick add)
     */
    public function quickStoreCategory(Request $request)
    {
        $request->validate([
            'nama_kategori' => 'required|string|max:100|unique:kategoris,nama_kategori',
        ]);
        
        $category = Kategori::create([
            'nama_kategori' => $request->nama_kategori,
            'deskripsi' => $request->deskripsi ?? null,
        ]);
        
        return response()->json([
            'success' => true,
            'category' => $category,
            'message' => 'Kategori berhasil ditambahkan'
        ]);
    }
}