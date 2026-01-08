<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Permintaan;
use App\Models\PermintaanDetail;
use App\Models\Kategori;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use PDF;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class ReportController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // Data statistik utama
        $stats = $this->getGlobalStats();
        
        // Data untuk charts
        $monthlyRequestsData = $this->getMonthlyRequestsData();
        $requestStatusData = $this->getRequestStatusData();
        
        // Data statistik bulanan default
        $selectedMonth = request('month', date('Y-m'));
        $monthlyStats = $this->getMonthlyStatistics($selectedMonth);
        
        $data = compact(
            'user', 
            'stats', 
            'monthlyRequestsData', 
            'requestStatusData',
            'monthlyStats',
            'selectedMonth'
        );
        
        return view('admin.reports', $data);
    }
    
    /**
     * Get global statistics
     */
    private function getGlobalStats()
    {
        $totalRequests = Permintaan::count();
        $multiBarangRequests = Permintaan::has('details')->count();
        $singleBarangRequests = $totalRequests - $multiBarangRequests;
        
        // Hitung total item dari semua permintaan
        $totalItemsInRequests = 0;
        $requests = Permintaan::with('details')->get();
        foreach ($requests as $request) {
            if ($request->details->count() > 0) {
                $totalItemsInRequests += $request->details->sum('jumlah');
            } else {
                $totalItemsInRequests += $request->jumlah;
            }
        }
        
        // Hitung total item yang sudah terkirim
        $totalItemsInExpenditures = 0;
        $expenditures = Permintaan::with('details')->where('status', 'delivered')->get();
        foreach ($expenditures as $expenditure) {
            if ($expenditure->details->count() > 0) {
                $totalItemsInExpenditures += $expenditure->details->sum('jumlah');
            } else {
                $totalItemsInExpenditures += $expenditure->jumlah;
            }
        }
        
        return [
            'total_items' => Barang::count(),
            'total_requests' => $totalRequests,
            'total_expenditures' => Permintaan::where('status', 'delivered')->count(),
            'total_categories' => Kategori::count(),
            'good_stock' => Barang::whereRaw('stok > stok_minimal * 2')->count(),
            'low_stock' => Barang::whereRaw('stok <= stok_minimal * 2')->where('stok', '>', 0)->count(),
            'critical_stock' => Barang::whereRaw('stok <= stok_minimal')->where('stok', '>', 0)->count(),
            'out_of_stock' => Barang::where('stok', '<=', 0)->count(),
            'pending_requests' => Permintaan::where('status', 'pending')->count(),
            'approved_requests' => Permintaan::where('status', 'approved')->count(),
            'rejected_requests' => Permintaan::where('status', 'rejected')->count(),
            'delivered_requests' => Permintaan::where('status', 'delivered')->count(),
            'multi_barang_requests' => $multiBarangRequests,
            'single_barang_requests' => $singleBarangRequests,
            'total_items_in_requests' => $totalItemsInRequests,
            'total_items_in_expenditures' => $totalItemsInExpenditures,
        ];
    }
    
    /**
     * Get monthly statistics for specific month
     */
    public function getMonthlyStats(Request $request)
    {
        try {
            $request->validate(['month' => 'required|date_format:Y-m']);
            $stats = $this->getMonthlyStatistics($request->month);
            return response()->json($stats);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }
    
    /**
     * Helper method to get monthly statistics
     */
    private function getMonthlyStatistics($month)
    {
        $startDate = Carbon::parse($month . '-01')->startOfMonth();
        $endDate = Carbon::parse($month . '-01')->endOfMonth();
        
        // Inventory stats
        $inventoryItems = Barang::whereBetween('created_at', [$startDate, $endDate])->get();
        
        // Requests stats - dengan detail multi barang
        $requests = Permintaan::with(['details'])->whereBetween('created_at', [$startDate, $endDate])->get();
        
        // Hitung statistik untuk multi barang
        $totalItemsInRequests = 0;
        $multiBarangRequests = 0;
        $singleBarangRequests = 0;
        
        foreach ($requests as $request) {
            if ($request->details && $request->details->count() > 0) {
                // Multi barang
                $multiBarangRequests++;
                $totalItemsInRequests += $request->details->sum('jumlah');
            } else {
                // Single barang
                $singleBarangRequests++;
                $totalItemsInRequests += $request->jumlah;
            }
        }
        
        // Expenditures stats
        $expenditures = Permintaan::with(['details'])
            ->where('status', 'delivered')
            ->where(function($query) use ($startDate, $endDate) {
                $query->whereNotNull('delivered_at')
                      ->whereBetween('delivered_at', [$startDate, $endDate])
                      ->orWhere(function($q) use ($startDate, $endDate) {
                          $q->whereNull('delivered_at')
                            ->whereBetween('updated_at', [$startDate, $endDate]);
                      });
            })->get();
        
        // Hitung total item dalam pengeluaran
        $totalItemsInExpenditures = 0;
        foreach ($expenditures as $expenditure) {
            if ($expenditure->details && $expenditure->details->count() > 0) {
                $totalItemsInExpenditures += $expenditure->details->sum('jumlah');
            } else {
                $totalItemsInExpenditures += $expenditure->jumlah;
            }
        }
        
        return [
            'total_items' => $inventoryItems->count(),
            'good_stock' => $inventoryItems->filter(fn($item) => $item->stok > $item->stok_minimal * 2)->count(),
            'low_stock' => $inventoryItems->filter(fn($item) => $item->stok <= $item->stok_minimal * 2 && $item->stok > $item->stok_minimal && $item->stok > 0)->count(),
            'critical_stock' => $inventoryItems->filter(fn($item) => $item->stok <= $item->stok_minimal && $item->stok > 0)->count(),
            'out_of_stock' => $inventoryItems->filter(fn($item) => $item->stok <= 0)->count(),
            
            // Request stats dengan multi barang
            'total_requests' => $requests->count(),
            'pending_requests' => $requests->where('status', 'pending')->count(),
            'approved_requests' => $requests->where('status', 'approved')->count(),
            'rejected_requests' => $requests->where('status', 'rejected')->count(),
            'delivered_requests' => $requests->where('status', 'delivered')->count(),
            'total_items_in_requests' => $totalItemsInRequests,
            'multi_barang_requests' => $multiBarangRequests,
            'single_barang_requests' => $singleBarangRequests,
            
            // Expenditure stats
            'total_expenditures' => $expenditures->count(),
            'total_items_in_expenditures' => $totalItemsInExpenditures,
        ];
    }
    
    /**
     * Get monthly requests data for chart (12 months)
     */
    private function getMonthlyRequestsData()
    {
        $data = [];
        $labels = [];
        
        for ($i = 11; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $monthStart = $month->copy()->startOfMonth();
            $monthEnd = $month->copy()->endOfMonth();
            
            $count = Permintaan::whereBetween('created_at', [$monthStart, $monthEnd])->count();
            $data[] = $count;
            $labels[] = $month->translatedFormat('M');
        }
        
        return compact('labels', 'data');
    }
    
    /**
     * Get request status data for pie chart
     */
    private function getRequestStatusData()
    {
        $statuses = ['pending', 'approved', 'rejected', 'delivered'];
        $data = [];
        
        foreach ($statuses as $status) {
            $count = Permintaan::where('status', $status)->count();
            $total = Permintaan::count();
            $percentage = $total > 0 ? round(($count / $total) * 100, 1) : 0;
            
            $data[$status] = compact('count', 'percentage');
        }
        
        return $data;
    }
    
    /**
     * Method untuk menampilkan detail laporan via AJAX
     */
    public function viewDetails(Request $request)
    {
        try {
            $request->validate([
                'report_type' => 'required|in:inventory,requests,expenditures',
                'start_date' => 'nullable|date',
                'end_date' => 'nullable|date|after_or_equal:start_date',
            ]);
            
            $type = $request->report_type;
            $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::now()->startOfMonth();
            $endDate = $request->end_date ? Carbon::parse($request->end_date) : Carbon::now()->endOfMonth();
            
            return $this->generateReportTable($type, $startDate, $endDate);
            
        } catch (\Exception $e) {
            Log::error('Error in viewDetails: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return '<div class="alert alert-danger">
                <i class="bi bi-exclamation-triangle me-2"></i>
                Terjadi kesalahan sistem: ' . htmlspecialchars($e->getMessage()) . '
                <br><small>File: ' . $e->getFile() . ' Line: ' . $e->getLine() . '</small>
            </div>';
        }
    }
    
    /**
     * Generate HTML table for report details dengan kolom yang rapi
     */
    private function generateReportTable($type, $startDate, $endDate)
    {
        try {
            switch ($type) {
                case 'inventory':
                    return $this->generateInventoryTable($startDate, $endDate);
                case 'requests':
                    return $this->generateRequestsTable($startDate, $endDate);
                case 'expenditures':
                    return $this->generateExpendituresTable($startDate, $endDate);
                default:
                    return '<div class="alert alert-info">Jenis laporan tidak valid.</div>';
            }
        } catch (\Exception $e) {
            Log::error('Error generating report table: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return '<div class="alert alert-danger">
                <i class="bi bi-exclamation-triangle me-2"></i>
                Terjadi kesalahan saat memuat data: ' . htmlspecialchars($e->getMessage()) . '
                <br><small>File: ' . $e->getFile() . ' Line: ' . $e->getLine() . '</small>
            </div>';
        }
    }
    
    /**
     * Generate inventory report table dengan kolom yang rapi
     */
    private function generateInventoryTable($startDate, $endDate)
    {
        $data = Barang::with(['kategori', 'satuan', 'gudang'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('kategori_id')
            ->orderBy('nama_barang')
            ->get();
        
        if ($data->isEmpty()) {
            return '<tr><td colspan="9" class="text-center py-4 text-muted">
                    <i class="bi bi-inbox me-2"></i>Tidak ada data barang dalam periode yang dipilih.
                    </td></tr>';
        }
        
        $html = '';
        $no = 1;
        foreach ($data as $item) {
            $status = $item->stok <= 0 ? 'Habis' : 
                     ($item->stok <= $item->stok_minimal ? 'Kritis' : 
                     ($item->stok <= $item->stok_minimal * 2 ? 'Rendah' : 'Baik'));
            
            $statusClass = $item->stok <= 0 ? 'bg-danger' : 
                         ($item->stok <= $item->stok_minimal ? 'bg-danger' : 
                         ($item->stok <= $item->stok_minimal * 2 ? 'bg-warning' : 'bg-success'));
            
            $html .= '<tr>';
            $html .= '<td class="text-center">' . $no++ . '</td>';
            $html .= '<td class="code-cell"><strong>' . ($item->kode_barang ?? '-') . '</strong></td>';
            $html .= '<td class="text-truncate" title="' . htmlspecialchars($item->nama_barang) . '">' . ($item->nama_barang ?? '-') . '</td>';
            $html .= '<td>' . ($item->kategori->nama_kategori ?? '-') . '</td>';
            $html .= '<td class="text-center text-number">' . number_format($item->stok, 0, ',', '.') . '</td>';
            $html .= '<td class="text-center text-number">' . number_format($item->stok_minimal, 0, ',', '.') . '</td>';
            $html .= '<td>' . ($item->satuan->nama_satuan ?? '-') . '</td>';
            $html .= '<td>' . ($item->gudang->nama_gudang ?? '-') . '</td>';
            $html .= '<td class="text-center"><span class="badge ' . $statusClass . '">' . $status . '</span></td>';
            $html .= '</tr>';
        }
        
        return $html;
    }
    
    /**
     * Generate requests report table dengan kolom yang rapi
     */
    private function generateRequestsTable($startDate, $endDate)
    {
        $data = Permintaan::with(['user', 'satker', 'details.barang.satuan', 'details.satker'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'desc')
            ->get();
        
        if ($data->isEmpty()) {
            return '<tr><td colspan="10" class="text-center py-4 text-muted">
                    <i class="bi bi-inbox me-2"></i>Tidak ada data permintaan dalam periode yang dipilih.
                    </td></tr>';
        }
        
        $html = '';
        $no = 1;
        
        // Buat array untuk menyimpan script JavaScript
        $scriptData = [];
        
        foreach ($data as $item) {
            $statusText = $this->getStatusText($item->status);
            $statusClass = $this->getStatusClass($item->status);
            
            // Tentukan jenis permintaan
            $isMultiBarang = $item->details && $item->details->count() > 0;
            $jenisPermintaan = $isMultiBarang ? 
                '<span class="badge badge-multi">Multi Barang</span>' : 
                '<span class="badge badge-single">Single Barang</span>';
            
            // Hitung total item
            $totalItem = $isMultiBarang ? 
                $item->details->sum('jumlah') : 
                $item->jumlah;
            
            // Hitung jumlah barang berbeda
            $jumlahBarang = $isMultiBarang ? 
                $item->details->count() : 
                1;
            
            // Generate detail barang untuk modal
            $detailHtml = $this->generateBarangDetailsHtml($item);
            
            // Simpan detail HTML untuk JavaScript (tidak langsung di baris tabel)
            $scriptData[] = "detailData[" . $item->id . "] = `" . str_replace('`', '\`', $detailHtml) . "`;";
            
            $html .= '<tr>';
            $html .= '<td class="text-center">' . $no++ . '</td>';
            $html .= '<td class="code-cell"><strong>' . ($item->kode_permintaan ?? '-') . '</strong></td>';
            $html .= '<td class="date-cell text-center">' . ($item->created_at ? $item->created_at->format('d/m/Y H:i') : '-') . '</td>';
            $html .= '<td>' . ($item->user->name ?? '-') . '</td>';
            $html .= '<td class="text-truncate" title="' . htmlspecialchars($item->satker->nama_satker ?? '-') . '">' . ($item->satker->nama_satker ?? '-') . '</td>';
            $html .= '<td class="text-center">' . $jenisPermintaan . '</td>';
            $html .= '<td class="text-center text-number">' . $jumlahBarang . ' jenis</td>';
            $html .= '<td class="text-center text-number"><strong>' . number_format($totalItem, 0, ',', '.') . ' unit</strong></td>';
            
            // Kolom Status Permintaan
            $html .= '<td class="text-center">' . $this->getStatusBadge($item->status) . '</td>';
            
            // Kolom Tombol Detail
            $html .= '<td class="action-cell text-center">';
            $html .= '<button class="btn btn-sm btn-outline-info" onclick="showBarangDetails(' . $item->id . ')" title="Lihat Detail Barang">';
            $html .= '<i class="bi bi-list-ul"></i> Detail';
            $html .= '</button>';
            $html .= '</td>';
            $html .= '</tr>';
        }
        
        // Tambahkan script JavaScript di akhir (setelah semua baris tabel)
        if (!empty($scriptData)) {
            $html .= '<script type="text/javascript">';
            $html .= 'if (typeof detailData === "undefined") { window.detailData = {}; }';
            $html .= implode('', $scriptData);
            $html .= '</script>';
        }
        
        return $html;
    }
    
    /**
     * Generate HTML untuk detail barang dalam modal dengan status
     */
    private function generateBarangDetailsHtml($permintaan)
    {
        $isMultiBarang = $permintaan->details && $permintaan->details->count() > 0;
        
        if (!$isMultiBarang) {
            // Single barang dengan status
            return '
            <div class="table-responsive">
                <table class="table table-sm table-bordered">
                    <thead>
                        <tr>
                            <th>Barang</th>
                            <th>Kode</th>
                            <th class="text-center">Jumlah</th>
                            <th>Satuan</th>
                            <th>Satker</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>' . ($permintaan->barang->nama_barang ?? '-') . '</td>
                            <td>' . ($permintaan->barang->kode_barang ?? '-') . '</td>
                            <td class="text-center">' . number_format($permintaan->jumlah, 0, ',', '.') . '</td>
                            <td>' . ($permintaan->barang->satuan->nama_satuan ?? '-') . '</td>
                            <td>' . ($permintaan->satker->nama_satker ?? '-') . '</td>
                            <td class="text-center">' . $this->getStatusBadge($permintaan->status) . '</td>
                        </tr>
                    </tbody>
                </table>
            </div>';
        }
        
        // Multi barang dengan status per item
        $html = '
        <div class="table-responsive">
            <table class="table table-sm table-bordered">
                <thead>
                    <tr>
                        <th class="text-center">#</th>
                        <th>Barang</th>
                        <th>Kode</th>
                        <th class="text-center">Jumlah</th>
                        <th>Satuan</th>
                        <th>Satker</th>
                        <th class="text-center">Status</th>
                    </tr>
                </thead>
                <tbody>';
        
        $no = 1;
        foreach ($permintaan->details as $detail) {
            $html .= '
                <tr>
                    <td class="text-center">' . $no++ . '</td>
                    <td>' . ($detail->barang->nama_barang ?? '-') . '</td>
                    <td>' . ($detail->barang->kode_barang ?? '-') . '</td>
                    <td class="text-center">' . number_format($detail->jumlah, 0, ',', '.') . '</td>
                    <td>' . ($detail->barang->satuan->nama_satuan ?? '-') . '</td>
                    <td>' . ($detail->satker->nama_satker ?? $permintaan->satker->nama_satker ?? '-') . '</td>
                    <td class="text-center">' . $this->getStatusBadge($detail->status ?? $permintaan->status) . '</td>
                </tr>';
        }
        
        $html .= '
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="3" class="text-end">Total:</th>
                        <th class="text-center">' . number_format($permintaan->details->sum('jumlah'), 0, ',', '.') . '</th>
                        <th colspan="3" class="text-start">unit</th>
                    </tr>
                </tfoot>
            </table>
        </div>';
        
        return $html;
    }
    
    /**
     * Get status badge HTML
     */
    private function getStatusBadge($status)
    {
        $statusText = $this->getStatusText($status);
        $statusClass = $this->getStatusClass($status);
        
        return '<span class="badge ' . $statusClass . '">' . $statusText . '</span>';
    }
    
    /**
     * Generate expenditures report table dengan kolom yang rapi
     */
    private function generateExpendituresTable($startDate, $endDate)
    {
        $data = Permintaan::with(['user', 'barang', 'satker', 'details.barang.satuan', 'details.satker'])
            ->where('status', 'delivered')
            ->where(function($query) use ($startDate, $endDate) {
                $query->whereNotNull('delivered_at')
                      ->whereBetween('delivered_at', [$startDate, $endDate])
                      ->orWhere(function($q) use ($startDate, $endDate) {
                          $q->whereNull('delivered_at')
                            ->whereBetween('updated_at', [$startDate, $endDate]);
                      });
            })
            ->orderBy('delivered_at', 'desc')
            ->orderBy('updated_at', 'desc')
            ->get();
        
        if ($data->isEmpty()) {
            return '<tr><td colspan="8" class="text-center py-4 text-muted">
                    <i class="bi bi-inbox me-2"></i>Tidak ada data pengeluaran dalam periode yang dipilih.
                    </td></tr>';
        }
        
        $html = '';
        $no = 1;
        foreach ($data as $item) {
            $isMultiBarang = $item->details && $item->details->count() > 0;
            $jenis = $isMultiBarang ? 'Multi Barang' : 'Single Barang';
            
            $jumlahBarang = $isMultiBarang ? 
                $item->details->count() . ' jenis' : 
                '1 jenis';
            
            $totalItem = $isMultiBarang ? 
                $item->details->sum('jumlah') : 
                $item->jumlah;
            
            $tanggal = '-';
            if ($item->delivered_at) {
                try {
                    $tanggal = Carbon::parse($item->delivered_at)->format('d/m/Y H:i');
                } catch (\Exception $e) {
                    $tanggal = $item->delivered_at;
                }
            } elseif ($item->updated_at) {
                $tanggal = $item->updated_at->format('d/m/Y H:i') . ' (update)';
            }
            
            $html .= '<tr>';
            $html .= '<td class="text-center">' . $no++ . '</td>';
            $html .= '<td class="code-cell"><strong>' . ($item->kode_permintaan ?? '-') . '</strong></td>';
            $html .= '<td class="date-cell text-center">' . $tanggal . '</td>';
            $html .= '<td class="text-center">';
            $html .= $isMultiBarang ? 
                '<span class="badge badge-multi">Multi Barang</span>' : 
                '<span class="badge badge-single">Single Barang</span>';
            $html .= '</td>';
            $html .= '<td class="text-center text-number">' . $jumlahBarang . '</td>';
            $html .= '<td class="text-center text-number"><strong>' . number_format($totalItem, 0, ',', '.') . ' unit</strong></td>';
            $html .= '<td class="text-truncate" title="' . htmlspecialchars($item->satker->nama_satker ?? '-') . '">' . ($item->satker->nama_satker ?? '-') . '</td>';
            $html .= '<td class="text-truncate" title="' . htmlspecialchars($item->keperluan ?? '-') . '">' . ($item->keperluan ?? '-') . '</td>';
            $html .= '</tr>';
        }
        
        return $html;
    }
    
    public function export(Request $request, $type = null)
    {
        try {
            if (!$type) {
                $type = $request->type;
            }
            
            $request->validate([
                'type' => 'sometimes|required|in:inventory,requests,expenditures',
                'format' => 'required|in:csv,excel,pdf',
                'start_date' => 'nullable|date',
                'end_date' => 'nullable|date|after_or_equal:start_date',
            ]);
            
            $format = $request->format;
            $startDate = $request->start_date ? Carbon::parse($request->start_date) : null;
            $endDate = $request->end_date ? Carbon::parse($request->end_date) : null;
            
            switch ($type) {
                case 'inventory':
                    return $this->exportInventory($format, $startDate, $endDate);
                case 'requests':
                    return $this->exportRequests($format, $startDate, $endDate);
                case 'expenditures':
                    return $this->exportExpenditures($format, $startDate, $endDate);
                default:
                    return back()->with('error', 'Jenis laporan tidak valid');
            }
        } catch (\Exception $e) {
            Log::error('Error in export: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat mengekspor data: ' . $e->getMessage());
        }
    }
    
    /**
     * Export inventory data
     */
    private function exportInventory($format, $startDate, $endDate)
    {
        $query = Barang::with(['kategori', 'satuan', 'gudang']);
        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }
        $data = $query->get();
        
        $filename = 'laporan-barang-' . date('Y-m-d');
        $headers = ['No', 'Kode Barang', 'Nama Barang', 'Kategori', 'Stok', 'Stok Minimal', 'Satuan', 'Gudang', 'Status Stok'];
        
        $rows = [];
        $no = 1;
        foreach ($data as $item) {
            $status = $item->stok <= 0 ? 'Habis' : 
                     ($item->stok <= $item->stok_minimal ? 'Kritis' : 
                     ($item->stok <= $item->stok_minimal * 2 ? 'Rendah' : 'Baik'));
            
            $rows[] = [
                $no++,
                $item->kode_barang,
                $item->nama_barang,
                $item->kategori->nama_kategori ?? '',
                $item->stok,
                $item->stok_minimal,
                $item->satuan->nama_satuan ?? '',
                $item->gudang->nama_gudang ?? '',
                $status
            ];
        }
        
        switch ($format) {
            case 'pdf':
                return $this->exportToPdf('Laporan Stok Barang', $headers, $rows, $filename . '.pdf');
            case 'excel':
                return $this->exportToExcel('Laporan Stok Barang', $headers, $rows, $filename);
            default:
                return $this->exportToCsv($headers, $rows, $filename . '.csv');
        }
    }
    
    /**
     * Export requests data dengan dukungan multi barang dan status
     */
    private function exportRequests($format, $startDate, $endDate)
    {
        $query = Permintaan::with(['user', 'barang', 'satker', 'details.barang.satuan', 'details.satker']);
        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }
        $data = $query->get();
        
        $filename = 'laporan-permintaan-' . date('Y-m-d');
        $headers = ['No', 'Kode Permintaan', 'Tanggal', 'Pemohon', 'Satker', 'Jenis', 'Jumlah Barang', 'Total Item', 'Status'];
        
        $rows = [];
        $no = 1;
        foreach ($data as $requestItem) {
            $isMultiBarang = $requestItem->details && $requestItem->details->count() > 0;
            $jenis = $isMultiBarang ? 'Multi Barang' : 'Single Barang';
            
            $jumlahBarang = $isMultiBarang ? 
                $requestItem->details->count() . ' jenis' : 
                '1 jenis';
            
            $totalItem = $isMultiBarang ? 
                $requestItem->details->sum('jumlah') : 
                $requestItem->jumlah;
            
            $rows[] = [
                $no++,
                $requestItem->kode_permintaan,
                $requestItem->created_at ? $requestItem->created_at->format('d/m/Y H:i') : '',
                $requestItem->user->name ?? '',
                $requestItem->satker->nama_satker ?? '',
                $jenis,
                $jumlahBarang,
                $totalItem,
                $this->getStatusText($requestItem->status)
            ];
        }
        
        switch ($format) {
            case 'pdf':
                return $this->exportToPdf('Laporan Permintaan Barang', $headers, $rows, $filename . '.pdf');
            case 'excel':
                return $this->exportToExcel('Laporan Permintaan Barang', $headers, $rows, $filename);
            default:
                return $this->exportToCsv($headers, $rows, $filename . '.csv');
        }
    }
    
    /**
     * Export expenditures data dengan dukungan multi barang
     */
    private function exportExpenditures($format, $startDate, $endDate)
    {
        $query = Permintaan::with(['user', 'barang', 'satker', 'details.barang.satuan', 'details.satker'])
            ->where('status', 'delivered');
            
        if ($startDate && $endDate) {
            $query->where(function($q) use ($startDate, $endDate) {
                $q->whereNotNull('delivered_at')
                  ->whereBetween('delivered_at', [$startDate, $endDate])
                  ->orWhere(function($q2) use ($startDate, $endDate) {
                      $q2->whereNull('delivered_at')
                         ->whereBetween('updated_at', [$startDate, $endDate]);
                  });
            });
        }
        
        $data = $query->get();
        
        $filename = 'laporan-pengeluaran-' . date('Y-m-d');
        $headers = ['No', 'Kode Permintaan', 'Tanggal Pengiriman', 'Jenis', 'Jumlah Barang', 'Total Item', 'Penerima (Satker)', 'Keperluan'];
        
        $rows = [];
        $no = 1;
        foreach ($data as $expenditure) {
            $isMultiBarang = $expenditure->details && $expenditure->details->count() > 0;
            $jenis = $isMultiBarang ? 'Multi Barang' : 'Single Barang';
            
            $jumlahBarang = $isMultiBarang ? 
                $expenditure->details->count() . ' jenis' : 
                '1 jenis';
            
            $totalItem = $isMultiBarang ? 
                $expenditure->details->sum('jumlah') : 
                $expenditure->jumlah;
            
            $deliveredAtFormatted = '';
            if ($expenditure->delivered_at) {
                try {
                    $deliveredAtFormatted = Carbon::parse($expenditure->delivered_at)->format('d/m/Y H:i');
                } catch (\Exception $e) {
                    $deliveredAtFormatted = $expenditure->delivered_at;
                }
            } elseif ($expenditure->updated_at) {
                $deliveredAtFormatted = $expenditure->updated_at->format('d/m/Y H:i') . ' (update)';
            }
            
            $rows[] = [
                $no++,
                $expenditure->kode_permintaan,
                $deliveredAtFormatted,
                $jenis,
                $jumlahBarang,
                $totalItem,
                $expenditure->satker->nama_satker ?? '',
                $expenditure->keperluan ?? ''
            ];
        }
        
        switch ($format) {
            case 'pdf':
                return $this->exportToPdf('Laporan Pengeluaran Barang', $headers, $rows, $filename . '.pdf');
            case 'excel':
                return $this->exportToExcel('Laporan Pengeluaran Barang', $headers, $rows, $filename);
            default:
                return $this->exportToCsv($headers, $rows, $filename . '.csv');
        }
    }
    
    /**
     * Get chart data via AJAX
     */
    public function getChartData(Request $request)
    {
        $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::now()->subMonths(11)->startOfMonth();
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : Carbon::now()->endOfMonth();
        
        // Monthly requests data
        $monthlyData = [];
        $monthlyLabels = [];
        
        $current = $startDate->copy();
        while ($current <= $endDate) {
            $monthStart = $current->copy()->startOfMonth();
            $monthEnd = $current->copy()->endOfMonth();
            
            $count = Permintaan::whereBetween('created_at', [$monthStart, $monthEnd])->count();
            $monthlyData[] = $count;
            $monthlyLabels[] = $current->translatedFormat('M Y');
            $current->addMonth();
        }
        
        // Status data
        $statusData = [
            'pending' => Permintaan::where('status', 'pending')->whereBetween('created_at', [$startDate, $endDate])->count(),
            'approved' => Permintaan::where('status', 'approved')->whereBetween('created_at', [$startDate, $endDate])->count(),
            'rejected' => Permintaan::where('status', 'rejected')->whereBetween('created_at', [$startDate, $endDate])->count(),
            'delivered' => Permintaan::where('status', 'delivered')
                ->where(function($query) use ($startDate, $endDate) {
                    $query->whereNotNull('delivered_at')
                          ->whereBetween('delivered_at', [$startDate, $endDate])
                          ->orWhere(function($q) use ($startDate, $endDate) {
                              $q->whereNull('delivered_at')
                                ->whereBetween('updated_at', [$startDate, $endDate]);
                          });
                })->count(),
        ];
        
        return response()->json(compact('monthlyLabels', 'monthlyData', 'statusData'));
    }
    
    /**
     * Export to CSV
     */
    private function exportToCsv($headers, $rows, $filename)
    {
        return response()->streamDownload(function () use ($headers, $rows) {
            $output = fopen('php://output', 'w');
            fputs($output, chr(0xEF) . chr(0xBB) . chr(0xBF)); // BOM for UTF-8
            fputcsv($output, $headers);
            foreach ($rows as $row) {
                fputcsv($output, $row);
            }
            fclose($output);
        }, $filename);
    }
    
    /**
     * Export to PDF
     */
    private function exportToPdf($title, $headers, $data, $filename)
    {
        try {
            $html = '<!DOCTYPE html>
            <html>
            <head>
                <meta charset="UTF-8">
                <title>' . $title . '</title>
                <style>
                    body { font-family: Arial, sans-serif; font-size: 11px; }
                    h1 { text-align: center; color: #1e3a8a; margin-bottom: 5px; }
                    h2 { text-align: center; color: #333; margin-top: 0; margin-bottom: 15px; }
                    .subtitle { text-align: center; color: #666; margin-bottom: 15px; }
                    table { width: 100%; border-collapse: collapse; margin-top: 5px; font-size: 10px; }
                    th { background-color: #1e3a8a; color: white; padding: 8px; border: 1px solid #ddd; text-align: left; font-weight: bold; }
                    td { padding: 6px; border: 1px solid #ddd; }
                    tr:nth-child(even) { background-color: #f8fafc; }
                    .footer { text-align: center; margin-top: 20px; font-size: 9px; color: #666; }
                    .logo { text-align: center; margin-bottom: 15px; }
                    .page-break { page-break-before: always; }
                    .text-center { text-align: center; }
                    .text-right { text-align: right; }
                </style>
            </head>
            <body>
                <div class="logo">
                    <h1>SILOG POLRES</h1>
                    <h3>Sistem Logistik Kepolisian</h3>
                </div>
                <h2>' . $title . '</h2>
                <p class="subtitle">Tanggal Generate: ' . date('d/m/Y H:i') . '</p>';
            
            if (!empty($data)) {
                $html .= '<table>
                    <thead>
                        <tr>';
                foreach ($headers as $header) {
                    $html .= '<th>' . $header . '</th>';
                }
                $html .= '</tr>
                    </thead>
                    <tbody>';
                
                $rowCount = 0;
                foreach ($data as $row) {
                    $rowCount++;
                    $html .= '<tr>';
                    foreach ($row as $index => $cell) {
                        // Format angka rata kanan
                        if (is_numeric($cell) && !in_array($headers[$index], ['Kode Permintaan', 'Kode Barang'])) {
                            $html .= '<td class="text-right">' . htmlspecialchars($cell) . '</td>';
                        } else {
                            $html .= '<td>' . htmlspecialchars($cell) . '</td>';
                        }
                    }
                    $html .= '</tr>';
                    
                    // Tambahkan page break setiap 30 baris
                    if ($rowCount % 30 == 0) {
                        $html .= '</tbody></table><div class="page-break"></div>';
                        $html .= '<table><thead><tr>';
                        foreach ($headers as $header) {
                            $html .= '<th>' . $header . '</th>';
                        }
                        $html .= '</tr></thead><tbody>';
                    }
                }
                
                $html .= '</tbody>
                </table>';
            } else {
                $html .= '<p style="text-align: center; color: #999; padding: 40px;">Tidak ada data untuk ditampilkan.</p>';
            }
            
            $html .= '<div class="footer">
                    <hr style="border: none; border-top: 1px solid #ddd; margin: 15px 0;">
                    Generated by SILOG Polres - Sistem Logistik<br>
                    ' . date('d/m/Y H:i') . ' | Halaman <span class="page-number"></span>
                </div>
            </body>
            </html>';
            
            $pdf = PDF::loadHTML($html);
            $pdf->setPaper('A4', 'landscape');
            
            return $pdf->download($filename);
            
        } catch (\Exception $e) {
            // Fallback ke CSV jika PDF gagal
            Log::warning('PDF export failed, falling back to CSV: ' . $e->getMessage());
            return $this->exportToCsv($headers, $data, str_replace('.pdf', '.csv', $filename));
        }
    }
    
    /**
     * Export to Excel menggunakan PhpSpreadsheet (lebih rapi)
     */
    private function exportToExcel($title, $headers, $data, $filename)
    {
        try {
            // Buat spreadsheet baru
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            
            // Set properties dokumen
            $spreadsheet->getProperties()
                ->setCreator('SILOG Polres')
                ->setLastModifiedBy('SILOG Polres')
                ->setTitle($title)
                ->setSubject($title)
                ->setDescription('Laporan generated by SILOG Polres');
            
            // ========== HEADER DAN JUDUL ==========
            
            // Baris 1: Judul Utama
            $sheet->setCellValue('A1', 'SILOG POLRES - SISTEM LOGISTIK KEPOLISIAN');
            $sheet->mergeCells('A1:' . $this->getColumnLetter(count($headers) - 1) . '1');
            $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
            $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('A1')->getFont()->getColor()->setARGB('FF1E3A8A'); // Warna biru
            
            // Baris 2: Judul Laporan
            $sheet->setCellValue('A2', $title);
            $sheet->mergeCells('A2:' . $this->getColumnLetter(count($headers) - 1) . '2');
            $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(12);
            $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            
            // Baris 3: Tanggal Generate
            $sheet->setCellValue('A3', 'Tanggal Generate: ' . date('d/m/Y H:i'));
            $sheet->mergeCells('A3:' . $this->getColumnLetter(count($headers) - 1) . '3');
            $sheet->getStyle('A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('A3')->getFont()->setItalic(true);
            
            // Spasi
            $sheet->setCellValue('A4', '');
            
            // ========== HEADER TABEL ==========
            
            $headerRow = 5;
            $columnIndex = 0;
            
            foreach ($headers as $header) {
                $columnLetter = $this->getColumnLetter($columnIndex);
                $sheet->setCellValue($columnLetter . $headerRow, $header);
                
                // Style untuk header
                $sheet->getStyle($columnLetter . $headerRow)->getFont()->setBold(true);
                $sheet->getStyle($columnLetter . $headerRow)->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                    ->setVertical(Alignment::VERTICAL_CENTER);
                
                $sheet->getStyle($columnLetter . $headerRow)->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FF1E3A8A'); // Warna biru
                
                $sheet->getStyle($columnLetter . $headerRow)->getFont()->getColor()->setARGB('FFFFFFFF'); // Putih
                
                $sheet->getStyle($columnLetter . $headerRow)->getBorders()->getAllBorders()
                    ->setBorderStyle(Border::BORDER_THIN)
                    ->getColor()->setARGB('FF000000');
                
                // Auto size untuk kolom
                $sheet->getColumnDimension($columnLetter)->setAutoSize(true);
                
                $columnIndex++;
            }
            
            // ========== DATA ROWS ==========
            
            $dataRow = $headerRow + 1;
            
            if (!empty($data)) {
                foreach ($data as $rowIndex => $row) {
                    $columnIndex = 0;
                    
                    foreach ($row as $cellValue) {
                        $columnLetter = $this->getColumnLetter($columnIndex);
                        $sheet->setCellValue($columnLetter . $dataRow, $cellValue);
                        
                        // Style untuk sel data
                        $sheet->getStyle($columnLetter . $dataRow)->getBorders()->getAllBorders()
                            ->setBorderStyle(Border::BORDER_THIN)
                            ->getColor()->setARGB('FFCCCCCC');
                        
                        // Format angka jika perlu
                        if (is_numeric($cellValue) && !preg_match('/[a-zA-Z]/', $cellValue) && $columnIndex > 0) {
                            $sheet->getStyle($columnLetter . $dataRow)
                                ->getNumberFormat()
                                ->setFormatCode(NumberFormat::FORMAT_NUMBER);
                            $sheet->getStyle($columnLetter . $dataRow)
                                ->getAlignment()
                                ->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                        } else {
                            $sheet->getStyle($columnLetter . $dataRow)
                                ->getAlignment()
                                ->setHorizontal(Alignment::HORIZONTAL_LEFT)
                                ->setWrapText(true);
                        }
                        
                        // Warna background untuk baris genap
                        if ($rowIndex % 2 == 0) {
                            $sheet->getStyle($columnLetter . $dataRow)->getFill()
                                ->setFillType(Fill::FILL_SOLID)
                                ->getStartColor()->setARGB('FFF8FAFC'); // Abu-abu muda
                        }
                        
                        $columnIndex++;
                    }
                    
                    $dataRow++;
                }
                
                // Auto filter untuk header
                $sheet->setAutoFilter('A' . $headerRow . ':' . $this->getColumnLetter(count($headers) - 1) . ($dataRow - 1));
                
                // Freeze header row
                $sheet->freezePane('A' . ($headerRow + 1));
                
            } else {
                // Jika tidak ada data
                $sheet->setCellValue('A' . $dataRow, 'Tidak ada data untuk ditampilkan');
                $sheet->mergeCells('A' . $dataRow . ':' . $this->getColumnLetter(count($headers) - 1) . $dataRow);
                $sheet->getStyle('A' . $dataRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('A' . $dataRow)->getFont()->setItalic(true);
                $dataRow++;
            }
            
            // ========== FOOTER ==========
            
            $footerRow = $dataRow + 2;
            $sheet->setCellValue('A' . $footerRow, 'Generated by SILOG Polres - Sistem Logistik');
            $sheet->mergeCells('A' . $footerRow . ':' . $this->getColumnLetter(count($headers) - 1) . $footerRow);
            $sheet->getStyle('A' . $footerRow)->getFont()->setItalic(true);
            $sheet->getStyle('A' . $footerRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            
            // Set judul sheet
            $sheet->setTitle('Laporan');
            
            // ========== EXPORT KE FILE ==========
            
            // Buat writer
            $writer = new Xlsx($spreadsheet);
            
            // Tambahkan ekstensi .xlsx jika belum ada
            if (!str_ends_with($filename, '.xlsx')) {
                $filename .= '.xlsx';
            }
            
            // Stream file ke browser
            return response()->streamDownload(
                function () use ($writer) {
                    $writer->save('php://output');
                },
                $filename,
                [
                    'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    'Cache-Control' => 'max-age=0',
                ]
            );
            
        } catch (\Exception $e) {
            Log::error('Excel export failed: ' . $e->getMessage());
            
            // Fallback ke CSV jika Excel gagal
            Log::warning('Excel export failed, falling back to CSV: ' . $e->getMessage());
            return $this->exportToCsv($headers, $data, str_replace('.xlsx', '.csv', $filename));
        }
    }
    
    /**
     * Helper method untuk mendapatkan huruf kolom dari index
     */
    private function getColumnLetter($index)
    {
        $letters = range('A', 'Z');
        
        if ($index < 26) {
            return $letters[$index];
        } else {
            $firstLetter = floor($index / 26) - 1;
            $secondLetter = $index % 26;
            return $letters[$firstLetter] . $letters[$secondLetter];
        }
    }
    
    /**
     * Get status text
     */
    private function getStatusText($status)
    {
        return match($status) {
            'pending' => 'Pending',
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
            'processing' => 'Diproses',
            'delivered' => 'Terkirim',
            default => $status
        };
    }
    
    /**
     * Get status class
     */
    private function getStatusClass($status)
    {
        return match($status) {
            'pending' => 'badge-pending',
            'approved' => 'badge-approved',
            'rejected' => 'badge-rejected',
            'processing' => 'badge-processing',
            'delivered' => 'badge-delivered',
            default => ''
        };
    }
}