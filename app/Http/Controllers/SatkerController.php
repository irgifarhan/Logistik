<?php

namespace App\Http\Controllers;

use App\Models\Satker;
use App\Models\User;
use App\Models\Permintaan;
use Illuminate\Http\Request;

class SatkerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();
        
        // Ambil data satker dengan jumlah user
        $satkers = Satker::withCount('users')
            ->latest()
            ->paginate(10);
        
        // Statistik tanpa menggunakan kolom 'status' yang tidak ada
        $stats = [
            'total_satker' => Satker::count(),
            'total_users' => User::count(),
            'total_permintaan' => Permintaan::count(),
            'satker_dengan_user' => Satker::has('users')->count(),
        ];
        
        return view('admin.satker', compact('user', 'satkers', 'stats'));
    }
    
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'kode_satker' => 'required|unique:satkers,kode_satker|max:20',
            'nama_satker' => 'required|max:100',
            'alamat' => 'required',
            'telepon' => 'nullable|max:20',
            'email' => 'nullable|email|max:100',
            'nama_kepala' => 'nullable|max:100',
            'pangkat_kepala' => 'nullable|max:50',
            'nrp_kepala' => 'nullable|max:30',
        ]);
        
        try {
            Satker::create($request->all());
            
            return redirect()->route('admin.satker')
                ->with('success', 'Satker berhasil ditambahkan.');
                
        } catch (\Exception $e) {
            return redirect()->route('admin.satker')
                ->with('error', 'Gagal menambahkan satker: ' . $e->getMessage());
        }
    }
    
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Satker $satker)
    {
        return response()->json([
            'success' => true,
            'data' => $satker
        ]);
    }
    
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Satker $satker)
    {
        $request->validate([
            'kode_satker' => 'required|unique:satkers,kode_satker,' . $satker->id . '|max:20',
            'nama_satker' => 'required|max:100',
            'alamat' => 'required',
            'telepon' => 'nullable|max:20',
            'email' => 'nullable|email|max:100',
            'nama_kepala' => 'nullable|max:100',
            'pangkat_kepala' => 'nullable|max:50',
            'nrp_kepala' => 'nullable|max:30',
        ]);
        
        try {
            $satker->update($request->all());
            
            return redirect()->route('admin.satker')
                ->with('success', 'Satker berhasil diperbarui.');
                
        } catch (\Exception $e) {
            return redirect()->route('admin.satker')
                ->with('error', 'Gagal memperbarui satker: ' . $e->getMessage());
        }
    }
    
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Satker $satker)
    {
        try {
            // Cek apakah satker memiliki user
            if ($satker->users()->count() > 0) {
                return redirect()->route('admin.satker')
                    ->with('error', 'Tidak dapat menghapus satker yang masih memiliki user.');
            }
            
            $satker->delete();
            
            return redirect()->route('admin.satker')
                ->with('success', 'Satker berhasil dihapus.');
                
        } catch (\Exception $e) {
            return redirect()->route('admin.satker')
                ->with('error', 'Gagal menghapus satker: ' . $e->getMessage());
        }
    }
    
    /**
     * Get satker details for AJAX request
     */
    public function getDetails($id)
    {
        try {
            $satker = Satker::withCount(['users', 'permintaans'])->findOrFail($id);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'satker' => $satker,
                    'users_count' => $satker->users_count,
                    'permintaans_count' => $satker->permintaans_count ?? 0,
                    'recent_users' => $satker->users()->latest()->take(5)->get(),
                    'recent_permintaans' => $satker->permintaans()->latest()->take(5)->get() ?? collect(),
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Satker tidak ditemukan'
            ], 404);
        }
    }
    
    /**
     * Get all satkers for dropdown/select
     */
    public function getSatkersForSelect()
    {
        $satkers = Satker::select('id', 'kode_satker', 'nama_satker')
            ->orderBy('nama_satker')
            ->get();
            
        return response()->json([
            'success' => true,
            'data' => $satkers
        ]);
    }
}