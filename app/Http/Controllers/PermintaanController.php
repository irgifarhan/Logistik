<?php

namespace App\Http\Controllers;

use App\Models\Permintaan;
use App\Models\PermintaanDetail; // Tambahkan model ini jika belum ada
use App\Models\Barang;
use App\Models\Satker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\ActivityLogController;

class PermintaanController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // Query dengan filter status jika ada
        $status = request('status');
        $search = request('search');
        $satker = request('satker');
        
        $requests = Permintaan::with([
                'user', 
                'barang.satuan',  // Load satuan melalui barang
                'satker',
                'details.barang.satuan', // Load details dengan barang dan satuan
                'details.satker'         // Load satker untuk setiap detail
            ])
            ->when($status && $status != 'all', function($query) use ($status) {
                return $query->where('status', $status);
            })
            ->when($search, function($query) use ($search) {
                return $query->where(function($q) use ($search) {
                    $q->where('kode_permintaan', 'like', "%{$search}%")
                      ->orWhereHas('user', function($userQuery) use ($search) {
                          $userQuery->where('name', 'like', "%{$search}%");
                      })
                      ->orWhereHas('barang', function($barangQuery) use ($search) {
                          $barangQuery->where('nama_barang', 'like', "%{$search}%")
                                      ->orWhere('kode_barang', 'like', "%{$search}%");
                      })
                      ->orWhereHas('details.barang', function($detailQuery) use ($search) {
                          $detailQuery->where('nama_barang', 'like', "%{$search}%")
                                      ->orWhere('kode_barang', 'like', "%{$search}%");
                      });
                });
            })
            ->when($satker, function($query) use ($satker) {
                return $query->where('satker_id', $satker)
                            ->orWhereHas('details', function($q) use ($satker) {
                                $q->where('satker_id', $satker);
                            });
            })
            ->latest()
            ->paginate(10);
        
        $stats = [
            'total_requests' => Permintaan::count(),
            'pending_requests' => Permintaan::where('status', 'pending')->count(),
            'approved_requests' => Permintaan::where('status', 'approved')->count(),
            'rejected_requests' => Permintaan::where('status', 'rejected')->count(),
            'delivered_requests' => Permintaan::where('status', 'delivered')->count(),
        ];
        
        $satkers = Satker::all();
        $barangs = Barang::where('stok', '>', 0)->get();
        
        return view('admin.requests', compact('user', 'requests', 'stats', 'satkers', 'barangs'));
    }
    
    public function create()
    {
        $barangs = Barang::where('stok', '>', 0)->get();
        $satkers = Satker::all();
        return view('admin.requests.create', compact('barangs', 'satkers'));
    }
    
    public function store(Request $request)
    {
        // Validasi input
        $validated = $request->validate([
            'barang_id' => 'required|exists:barangs,id',
            'jumlah' => 'required|integer|min:1',
            'satker_id' => 'required|exists:satkers,id',
            'keperluan' => 'nullable|string',
        ]);
        
        // Check stock availability (hanya warning, tidak block)
        $barang = Barang::find($validated['barang_id']);
        if ($barang->stok < $validated['jumlah']) {
            \Log::warning('Stok tidak mencukupi untuk permintaan baru', [
                'barang' => $barang->nama_barang,
                'stok_tersedia' => $barang->stok,
                'jumlah_diminta' => $validated['jumlah']
            ]);
        }
        
        // Generate kode permintaan
        $lastRequest = Permintaan::latest()->first();
        $nextNumber = $lastRequest ? intval(substr($lastRequest->kode_permintaan, 4)) + 1 : 1;
        $kodePermintaan = 'PMT-' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
        
        $permintaan = Permintaan::create([
            'kode_permintaan' => $kodePermintaan,
            'user_id' => auth()->id(),
            'barang_id' => $validated['barang_id'],
            'jumlah' => $validated['jumlah'],
            'satker_id' => $validated['satker_id'],
            'keperluan' => $validated['keperluan'] ?? null,
            'status' => 'pending',
        ]);
        
        return redirect()->route('admin.requests')
            ->with('success', 'Permintaan berhasil dibuat dengan kode: ' . $kodePermintaan);
    }
    
    public function show(Permintaan $permintaan)
    {
        $permintaan->load([
            'user', 
            'barang.satuan', 
            'satker',
            'details.barang.satuan',
            'details.satker',
            'approver',
            'deliverer'
        ]);
        
        return response()->json([
            'success' => true,
            'request' => $permintaan
        ]);
    }
    
    public function approve(Request $request, Permintaan $permintaan)
    {
        // Validasi bahwa permintaan masih pending
        if ($permintaan->status != 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Permintaan sudah diproses.'
            ], 400);
        }
        
        try {
            // Simpan status lama untuk logging
            $oldStatus = $permintaan->status;
            
            // Update request status menjadi approved
            $permintaan->update([
                'status' => 'approved',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
                'catatan' => $permintaan->catatan ?? 'Disetujui oleh admin - Menunggu pengiriman',
            ]);
            
            // Jika ada details, update status details juga
            if ($permintaan->details()->exists()) {
                $permintaan->details()->update(['status' => 'approved']);
            }
            
            // Log aktivitas approval
            ActivityLogController::logApprovePermintaan($permintaan, $oldStatus);
            
            return response()->json([
                'success' => true,
                'message' => 'Permintaan berhasil disetujui. Stok akan dikurangi saat barang dikirim.'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function reject(Request $request, Permintaan $permintaan)
    {
        // Validasi bahwa permintaan masih pending
        if ($permintaan->status != 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Permintaan sudah diproses.'
            ], 400);
        }
        
        $validated = $request->validate([
            'reason' => 'required|string',
        ]);
        
        // Simpan status lama untuk logging
        $oldStatus = $permintaan->status;
        
        $permintaan->update([
            'status' => 'rejected',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'catatan' => $validated['reason'],
        ]);
        
        // Jika ada details, update status details juga
        if ($permintaan->details()->exists()) {
            $permintaan->details()->update(['status' => 'rejected']);
        }
        
        // Log aktivitas reject
        ActivityLogController::logRejectPermintaan($permintaan, $oldStatus, $validated['reason']);
        
        return response()->json([
            'success' => true,
            'message' => 'Permintaan berhasil ditolak.'
        ]);
    }
    
    public function markAsDelivered(Request $request, Permintaan $permintaan)
    {
        // Validasi bahwa permintaan sudah disetujui dan belum terkirim
        if ($permintaan->status != 'approved') {
            return response()->json([
                'success' => false,
                'message' => 'Hanya permintaan yang sudah disetujui yang bisa ditandai sebagai terkirim.'
            ], 400);
        }
        
        DB::beginTransaction();
        try {
            // Simpan status lama untuk logging
            $oldStatus = $permintaan->status;
            
            // Kurangi stok barang
            if ($permintaan->details()->exists()) {
                // Untuk multi barang, kurangi stok setiap barang
                foreach ($permintaan->details as $detail) {
                    if ($detail->barang->stok < $detail->jumlah) {
                        DB::rollback();
                        return response()->json([
                            'success' => false,
                            'message' => 'Stok tidak mencukupi untuk barang: ' . $detail->barang->nama_barang . 
                                        '. Stok tersedia: ' . $detail->barang->stok . 
                                        ', Jumlah yang diminta: ' . $detail->jumlah
                        ], 400);
                    }
                    $detail->barang->decrement('stok', $detail->jumlah);
                }
            } else {
                // Untuk single barang
                if ($permintaan->barang->stok < $permintaan->jumlah) {
                    DB::rollback();
                    return response()->json([
                        'success' => false,
                        'message' => 'Stok tidak mencukupi untuk dikirim. Stok tersedia: ' . 
                                    $permintaan->barang->stok . ', Jumlah yang diminta: ' . $permintaan->jumlah
                    ], 400);
                }
                $permintaan->barang->decrement('stok', $permintaan->jumlah);
            }
            
            // Update status menjadi delivered
            $permintaan->update([
                'status' => 'delivered',
                'delivered_at' => now(),
                'delivered_by' => auth()->id(),
                'catatan' => $request->input('catatan', $permintaan->catatan . ' - Barang telah dikirim'),
            ]);
            
            // Jika ada details, update status details juga
            if ($permintaan->details()->exists()) {
                $permintaan->details()->update(['status' => 'delivered']);
            }
            
            // Log aktivitas distribusi barang
            ActivityLogController::logDeliverPermintaan($permintaan, $oldStatus, $request->input('catatan'));
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Permintaan telah ditandai sebagai terkirim dan stok barang telah dikurangi.'
            ]);
            
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function forceDelivered(Request $request, Permintaan $permintaan)
    {
        // Hanya untuk admin senior
        if (!auth()->user()->hasRole('super_admin')) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki izin untuk melakukan ini.'
            ], 403);
        }
        
        if ($permintaan->status != 'approved') {
            return response()->json([
                'success' => false,
                'message' => 'Hanya permintaan yang sudah disetujui yang bisa ditandai sebagai terkirim.'
            ], 400);
        }
        
        $validated = $request->validate([
            'catatan' => 'required|string',
            'force_reason' => 'required|string'
        ]);
        
        DB::beginTransaction();
        try {
            // Simpan status lama untuk logging
            $oldStatus = $permintaan->status;
            
            // Update status menjadi delivered TANPA mengurangi stok
            $permintaan->update([
                'status' => 'delivered',
                'delivered_at' => now(),
                'delivered_by' => auth()->id(),
                'catatan' => $permintaan->catatan . ' - ' . $validated['catatan'] . ' (FORCE: ' . $validated['force_reason'] . ')',
            ]);
            
            // Jika ada details, update status details juga
            if ($permintaan->details()->exists()) {
                $permintaan->details()->update(['status' => 'delivered']);
            }
            
            // Log aktivitas distribusi paksa (tanpa mengurangi stok)
            $logData = [
                'permintaan_id' => $permintaan->id,
                'kode_permintaan' => $permintaan->kode_permintaan,
                'old_status' => $oldStatus,
                'new_status' => 'delivered',
                'force_reason' => $validated['force_reason'],
                'note' => 'Force delivered without stock reduction'
            ];
            ActivityLogController::logAction('force_deliver', 'Force delivered permintaan: ' . $permintaan->kode_permintaan, $logData);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Permintaan telah ditandai sebagai terkirim TANPA mengurangi stok (force).'
            ]);
            
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function getTransactionHistory($barangId = null)
    {
        // Menggunakan model Permintaan sebagai catatan transaksi
        $query = Permintaan::where('status', 'delivered')
            ->with(['barang', 'user', 'approver', 'deliverer', 'details.barang'])
            ->latest('delivered_at');
            
        if ($barangId) {
            $query->where(function($q) use ($barangId) {
                $q->where('barang_id', $barangId)
                  ->orWhereHas('details', function($detailQuery) use ($barangId) {
                      $detailQuery->where('barang_id', $barangId);
                  });
            });
        }
        
        $transactions = $query->get();
        
        return response()->json([
            'success' => true,
            'transactions' => $transactions
        ]);
    }
}