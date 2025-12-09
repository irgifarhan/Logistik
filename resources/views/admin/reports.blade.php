<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan | SILOG Polres</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- 
    
    -->
    <!-- LAPORAN BELOM RAMPUNG, MAISIH ADA USER SAMA SATKERNYA. SAMA GENERATE LAPORANNYA MASIH ANEH -->
    <!-- 
    
    -->
    <style>
        :root {
            --primary: #1e3a8a;
            --primary-light: #3b82f6;
            --secondary: #dc2626;
            --success: #10b981;
            --warning: #f59e0b;
            --info: #0ea5e9;
            --dark: #1e293b;
            --light: #f8fafc;
            --sidebar-width: 250px;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: #f1f5f9;
            color: var(--dark);
        }
        
        /* Sidebar */
        .sidebar {
            background: linear-gradient(180deg, var(--primary) 0%, #1e40af 100%);
            color: white;
            min-height: 100vh;
            width: var(--sidebar-width);
            position: fixed;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
            z-index: 1000;
        }
        
        .sidebar-brand {
            padding: 1.5rem;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        
        .sidebar-brand h3 {
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        
        .sidebar-brand p {
            font-size: 0.85rem;
            opacity: 0.8;
        }
        
        .sidebar-nav {
            padding: 1rem 0;
        }
        
        .nav-item {
            margin-bottom: 0.5rem;
        }
        
        .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 0.8rem 1.5rem;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.8rem;
            transition: all 0.3s;
        }
        
        .nav-link:hover, .nav-link.active {
            background-color: rgba(255,255,255,0.1);
            color: white;
            border-left: 4px solid var(--primary-light);
        }
        
        .nav-link i {
            width: 20px;
            text-align: center;
        }
        
        /* Main Content */
        .main-content {
            margin-left: var(--sidebar-width);
            padding: 1.5rem;
            min-height: 100vh;
        }
        
        /* Top Bar */
        .topbar {
            background: white;
            padding: 1rem 1.5rem;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            margin-bottom: 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .user-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .user-avatar {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 1.2rem;
        }
        
        .logout-btn {
            background: var(--secondary);
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            font-weight: 500;
            transition: background 0.3s;
        }
        
        .logout-btn:hover {
            background: #b91c1c;
        }
        
        /* Page Header */
        .page-header {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        .action-buttons {
            display: flex;
            gap: 0.5rem;
        }
        
        .btn-action {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }
        
        .stat-card {
            background: white;
            border-radius: 10px;
            padding: 1rem;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        
        .stat-content h5 {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 0.25rem;
        }
        
        .stat-content p {
            color: #64748b;
            font-size: 0.8rem;
            margin: 0;
        }
        
        /* Report Cards */
        .report-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .report-card {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            transition: transform 0.3s, box-shadow 0.3s;
            border-left: 4px solid var(--primary);
        }
        
        .report-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(0,0,0,0.1);
        }
        
        .report-icon {
            width: 50px;
            height: 50px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 1rem;
            color: white;
        }
        
        .report-icon.inventory {
            background-color: var(--primary);
        }
        
        .report-icon.requests {
            background-color: var(--warning);
        }
        
        .report-icon.expenditures {
            background-color: var(--info);
        }
        
        .report-icon.users {
            background-color: var(--success);
        }
        
        .report-content h5 {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 0.25rem;
        }
        
        .report-content p {
            color: var(--dark);
            font-weight: 600;
            margin-bottom: 0.25rem;
        }
        
        .report-content small {
            color: #64748b;
            font-size: 0.85rem;
        }
        
        /* Filter Bar */
        .filter-bar {
            background: white;
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        
        /* Alert */
        .alert-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            min-width: 300px;
        }
        
        /* Badges */
        .badge {
            padding: 0.4rem 0.8rem;
            font-weight: 600;
            border-radius: 6px;
            border: 1px solid rgba(0,0,0,0.1);
        }
        
        .badge-admin {
            background-color: #dbeafe !important;
            color: #1e40af !important;
            border-color: #60a5fa;
        }
        
        .badge-user {
            background-color: #f0f9ff !important;
            color: #0c4a6e !important;
            border-color: #7dd3fc;
        }
        
        .badge-operator {
            background-color: #f3e8ff !important;
            color: #6b21a8 !important;
            border-color: #c084fc;
        }
        
        .badge-pending {
            background-color: #fef3c7 !important;
            color: #92400e !important;
            border-color: #fbbf24;
        }
        
        .badge-approved {
            background-color: #d1fae5 !important;
            color: #065f46 !important;
            border-color: #10b981;
        }
        
        .badge-rejected {
            background-color: #fee2e2 !important;
            color: #991b1b !important;
            border-color: #ef4444;
        }
        
        .badge-processing {
            background-color: #dbeafe !important;
            color: #1e40af !important;
            border-color: #3b82f6;
        }
        
        .badge-delivered {
            background-color: #ede9fe !important;
            color: #5b21b6 !important;
            border-color: #8b5cf6;
        }
        
        /* Tables */
        .table-card {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            margin-bottom: 1.5rem;
        }
        
        .table th {
            background-color: #f8fafc;
            font-weight: 600;
            color: var(--dark);
            border-bottom: 2px solid #e2e8f0;
        }
        
        /* Charts Container */
        .charts-container {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        }
        
        /* Form Section Styling */
        .form-section {
            background: #f8fafc;
            border-radius: 8px;
            padding: 1.25rem;
            margin-bottom: 1.5rem;
            border-left: 4px solid var(--primary);
        }
        
        .form-section-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--primary);
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        /* Modal Form Styling */
        .modal-form {
            max-height: 70vh;
            overflow-y: auto;
            padding-right: 0.5rem;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                width: 70px;
            }
            
            .sidebar-brand h3, .sidebar-brand p, .nav-link span {
                display: none;
            }
            
            .main-content {
                margin-left: 70px;
            }
            
            .nav-link {
                justify-content: center;
                padding: 0.8rem;
            }
            
            .action-buttons {
                flex-direction: column;
                width: 100%;
            }
            
            .btn-action {
                width: 100%;
                justify-content: center;
            }
            
            .report-cards {
                grid-template-columns: 1fr;
            }
            
            .charts-container .row {
                flex-direction: column;
            }
            
            .charts-container .col-md-6 {
                margin-bottom: 1.5rem;
            }
            
            .charts-container .col-md-6:last-child {
                margin-bottom: 0;
            }
        }
        
        @media (max-width: 576px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .filter-bar .row {
                flex-direction: column;
            }
            
            .filter-bar .col-md-3 {
                margin-bottom: 0.75rem;
                width: 100%;
            }
        }
        
        /* Pagination Styling */
        .pagination {
            margin-bottom: 0;
        }
        
        .pagination .page-item .page-link {
            border: 1px solid #dee2e6;
            color: var(--primary);
            padding: 0.5rem 0.75rem;
        }
        
        .pagination .page-item.active .page-link {
            background-color: var(--primary);
            border-color: var(--primary);
            color: white;
        }
        
        .pagination .page-item.disabled .page-link {
            color: #6c757d;
            background-color: #f8fafc;
        }
        
        .pagination .page-item .page-link:hover {
            background-color: #e9ecef;
            border-color: #dee2e6;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-brand">
            <h3>SILOG</h3>
            <p>Manajemen Laporan</p>
        </div>
        
        <div class="sidebar-nav">
            <div class="nav-item">
                <a href="{{ route('admin.dashboard') }}" class="nav-link">
                    <i class="bi bi-speedometer2"></i>
                    <span>Dashboard</span>
                </a>
            </div>
            
            <div class="nav-item">
                <a href="{{ route('admin.inventory') }}" class="nav-link">
                    <i class="bi bi-box-seam"></i>
                    <span>Manajemen Barang</span>
                </a>
            </div>
            
            <div class="nav-item">
                <a href="{{ route('admin.requests') }}" class="nav-link">
                    <i class="bi bi-clipboard-check"></i>
                    <span>Permintaan Barang</span>
                </a>
            </div>
            
            <div class="nav-item">
                <a href="{{ route('admin.reports') }}" class="nav-link active">
                    <i class="bi bi-file-text"></i>
                    <span>Laporan</span>
                </a>
            </div>
            
            <!-- Hapus menu Manajemen User dan Pengaturan yang hanya untuk superadmin -->
        </div>
        
        <div class="sidebar-footer" style="padding: 1.5rem; position: absolute; bottom: 0; width: 100%;">
            <div class="text-center">
                <small style="opacity: 0.7;">Sistem Logistik Polres</small><br>
                <small style="opacity: 0.5;">v1.0.0</small>
            </div>
        </div>
    </div>
    
    <!-- Main Content -->
    <div class="main-content">
        <!-- Top Bar -->
        <div class="topbar">
            <h4 class="mb-0">Laporan Sistem</h4>
            <div class="user-info">
                <div class="user-avatar">
                    {{ substr($user->name, 0, 1) }}
                </div>
                <div>
                    <strong>{{ $user->name }}</strong><br>
                    <small class="text-muted">{{ $user->role }}</small>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="logout-btn">
                        <i class="bi bi-box-arrow-right"></i> Logout
                    </button>
                </form>
            </div>
        </div>
        
        <!-- Alert Messages -->
        @if(session('success'))
        <div class="alert-container">
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
        @endif
        
        @if(session('error'))
        <div class="alert-container">
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle me-2"></i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
        @endif
        
        <!-- Page Header -->
        <div class="page-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-1">Laporan Sistem SILOG</h5>
                    <p class="text-muted mb-0">Generate dan ekspor berbagai laporan sistem</p>
                </div>
                <div class="action-buttons">
                    <button class="btn btn-primary btn-action" data-bs-toggle="modal" data-bs-target="#generateReportModal">
                        <i class="bi bi-file-earmark-text"></i> Generate Laporan
                    </button>
                    <button class="btn btn-warning btn-action" onclick="printReport()">
                        <i class="bi bi-printer"></i> Cetak
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Stats Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-content">
                    <h5>{{ $stats['total_items'] ?? 0 }}</h5>
                    <p>Total Barang</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-content">
                    <h5>{{ $stats['total_categories'] ?? 0 }}</h5>
                    <p>Total Kategori</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-content">
                    <h5>{{ $stats['critical_stock'] ?? 0 }}</h5>
                    <p>Stok Kritis</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-content">
                    <h5>{{ $stats['out_of_stock'] ?? 0 }}</h5>
                    <p>Stok Habis</p>
                </div>
            </div>
        </div>
        
        <!-- Filter Bar -->
        <div class="filter-bar">
            <form method="GET" action="{{ route('admin.reports.generate') }}" id="filterForm">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="start_date" class="form-label">Dari Tanggal</label>
                        <input type="date" class="form-control" id="start_date" name="start_date" 
                               value="{{ date('Y-m-01') }}">
                    </div>
                    <div class="col-md-3">
                        <label for="end_date" class="form-label">Sampai Tanggal</label>
                        <input type="date" class="form-control" id="end_date" name="end_date" 
                               value="{{ date('Y-m-t') }}">
                    </div>
                    <div class="col-md-3">
                        <label for="report_type" class="form-label">Jenis Laporan</label>
                        <select class="form-select" id="report_type" name="report_type">
                            <option value="inventory">Laporan Stok Barang</option>
                            <option value="requests">Laporan Permintaan</option>
                            <option value="expenditures">Laporan Pengeluaran</option>
                            <option value="users">Laporan User</option>
                            <option value="satker">Laporan Satker</option>
                            <option value="summary">Laporan Ringkasan</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex gap-2 align-items-end">
                        <button type="submit" class="btn btn-primary flex-grow-1">
                            <i class="bi bi-filter"></i> Filter
                        </button>
                        <button type="button" class="btn btn-secondary" onclick="resetFilter()">
                            <i class="bi bi-arrow-clockwise"></i> Reset
                        </button>
                    </div>
                </div>
            </form>
        </div>
        
        <!-- Report Cards -->
        <div class="report-cards">
            <div class="report-card">
                <div class="report-icon inventory">
                    <i class="bi bi-box"></i>
                </div>
                <div class="report-content">
                    <h5>{{ $stats['total_items'] ?? 0 }}</h5>
                    <p>Total Barang</p>
                    <small class="text-muted">Data stok barang saat ini</small>
                </div>
            </div>
            
            <div class="report-card">
                <div class="report-icon requests">
                    <i class="bi bi-clipboard-check"></i>
                </div>
                <div class="report-content">
                    <h5>{{ $stats['total_requests'] ?? 0 }}</h5>
                    <p>Permintaan Barang</p>
                    <small class="text-muted">Total permintaan bulan ini</small>
                </div>
            </div>
            
            <div class="report-card">
                <div class="report-icon expenditures">
                    <i class="bi bi-cash-stack"></i>
                </div>
                <div class="report-content">
                    <h5>{{ $stats['total_expenditures'] ?? 0 }}</h5>
                    <p>Pengeluaran</p>
                    <small class="text-muted">Pengeluaran barang bulan ini</small>
                </div>
            </div>
            
            <div class="report-card">
                <div class="report-icon users">
                    <i class="bi bi-people"></i>
                </div>
                <div class="report-content">
                    <h5>{{ $stats['total_users'] ?? 0 }}</h5>
                    <p>Total User</p>
                    <small class="text-muted">User aktif sistem</small>
                </div>
            </div>
        </div>
        
        <!-- Charts Container -->
        <div class="charts-container">
            <div class="row">
                <div class="col-md-6">
                    <h6 class="mb-3">Statistik Permintaan Bulanan</h6>
                    <canvas id="monthlyRequestsChart" height="250"></canvas>
                </div>
                <div class="col-md-6">
                    <h6 class="mb-3">Distribusi Status Permintaan</h6>
                    <canvas id="requestStatusChart" height="250"></canvas>
                </div>
            </div>
        </div>
        
        <!-- Report Summary Table -->
        <div class="table-card">
            <h5 class="mb-3">Ringkasan Laporan</h5>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Jenis Laporan</th>
                            <th>Periode</th>
                            <th>Total Data</th>
                            <th>Detail Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Laporan Stok Barang</td>
                            <td>{{ date('F Y') }}</td>
                            <td>{{ $stats['total_items'] ?? 0 }} barang</td>
                            <td>
                                <span class="badge bg-success">{{ $stats['good_stock'] ?? 0 }} Baik</span>
                                <span class="badge bg-warning">{{ $stats['low_stock'] ?? 0 }} Rendah</span>
                                <span class="badge bg-danger">{{ $stats['critical_stock'] ?? 0 }} Kritis</span>
                                <span class="badge bg-secondary">{{ $stats['out_of_stock'] ?? 0 }} Habis</span>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-info" onclick="viewReport('inventory')" title="Lihat">
                                    <i class="bi bi-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-success" onclick="exportReport('inventory')" title="Export">
                                    <i class="bi bi-download"></i>
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td>Laporan Permintaan</td>
                            <td>{{ date('F Y') }}</td>
                            <td>{{ $stats['total_requests'] ?? 0 }} permintaan</td>
                            <td>
                                <span class="badge badge-pending">{{ $stats['pending_requests'] ?? 0 }} Pending</span>
                                <span class="badge badge-approved">{{ $stats['approved_requests'] ?? 0 }} Disetujui</span>
                                <span class="badge badge-rejected">{{ $stats['rejected_requests'] ?? 0 }} Ditolak</span>
                                <span class="badge badge-processing">{{ $stats['processing_requests'] ?? 0 }} Diproses</span>
                                <span class="badge badge-delivered">{{ $stats['delivered_requests'] ?? 0 }} Terkirim</span>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-info" onclick="viewReport('requests')" title="Lihat">
                                    <i class="bi bi-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-success" onclick="exportReport('requests')" title="Export">
                                    <i class="bi bi-download"></i>
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td>Laporan Pengeluaran</td>
                            <td>{{ date('F Y') }}</td>
                            <td>{{ $stats['total_expenditures'] ?? 0 }} pengeluaran</td>
                            <td>
                                <span class="badge bg-info">Pengeluaran Barang</span>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-info" onclick="viewReport('expenditures')" title="Lihat">
                                    <i class="bi bi-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-success" onclick="exportReport('expenditures')" title="Export">
                                    <i class="bi bi-download"></i>
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td>Laporan User</td>
                            <td>{{ date('F Y') }}</td>
                            <td>{{ $stats['total_users'] ?? 0 }} user</td>
                            <td>
                                <span class="badge badge-admin">{{ $stats['admin_users'] ?? 0 }} Admin</span>
                                <span class="badge badge-user">{{ $stats['user_users'] ?? 0 }} User</span>
                                <span class="badge badge-operator">{{ $stats['operator_users'] ?? 0 }} Operator</span>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-info" onclick="viewReport('users')" title="Lihat">
                                    <i class="bi bi-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-success" onclick="exportReport('users')" title="Export">
                                    <i class="bi bi-download"></i>
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td>Laporan Satker</td>
                            <td>{{ date('F Y') }}</td>
                            <td>{{ $stats['total_satker'] ?? 0 }} satker</td>
                            <td>
                                <span class="badge bg-primary">Satuan Kerja</span>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-info" onclick="viewReport('satker')" title="Lihat">
                                    <i class="bi bi-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-success" onclick="exportReport('satker')" title="Export">
                                    <i class="bi bi-download"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Report Details Table (Hidden by default) -->
        <div class="table-card" id="reportDetails" style="display: none;">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0" id="reportTitle">Detail Laporan</h5>
                <button class="btn btn-sm btn-secondary" onclick="hideReportDetails()">
                    <i class="bi bi-x"></i> Tutup
                </button>
            </div>
            <div class="table-responsive" id="reportTable">
                <!-- Report table will be loaded here -->
            </div>
        </div>
    </div>
    
    <!-- Generate Report Modal -->
    <div class="modal fade" id="generateReportModal" tabindex="-1" aria-labelledby="generateReportModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="generateReportModalLabel">Generate Laporan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="export_type" class="form-label">
                            Jenis Laporan
                            <span class="required-star">*</span>
                        </label>
                        <select class="form-select" id="export_type" name="type" required>
                            <option value="">Pilih Jenis Laporan</option>
                            <option value="inventory">Laporan Stok Barang</option>
                            <option value="requests">Laporan Permintaan</option>
                            <option value="expenditures">Laporan Pengeluaran</option>
                            <option value="users">Laporan User</option>
                            <option value="satker">Laporan Satker</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="export_format" class="form-label">
                            Format Export
                            <span class="required-star">*</span>
                        </label>
                        <select class="form-select" id="export_format" name="format" required>
                            <option value="csv">CSV</option>
                            <option value="excel">Excel</option>
                            <option value="pdf">PDF</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="export_start_date" class="form-label">Dari Tanggal</label>
                        <input type="date" class="form-control" id="export_start_date" name="start_date">
                    </div>
                    <div class="mb-3">
                        <label for="export_end_date" class="form-label">Sampai Tanggal</label>
                        <input type="date" class="form-control" id="export_end_date" name="end_date">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" onclick="downloadReport()">
                        <i class="bi bi-download me-1"></i> Generate & Download
                    </button>
                </div>
            </div>
        </div>
    </div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script>
    $(document).ready(function() {
        // Set default dates for export modal
        $('#export_start_date').val('{{ date("Y-m-01") }}');
        $('#export_end_date').val('{{ date("Y-m-t") }}');
        
        // Auto dismiss alerts
        setTimeout(() => {
            $('.alert').alert('close');
        }, 5000);
        
        // Initialize Charts
        initializeCharts();
    });
    
    // Initialize Charts dengan data dinamis dari Laravel
    function initializeCharts() {
        const monthlyRequestsCtx = document.getElementById('monthlyRequestsChart').getContext('2d');
        
        // Data dari controller - menggunakan escape JavaScript untuk array
        const monthlyLabels = {!! json_encode($monthlyRequestsData['labels'] ?? []) !!};
        const monthlyData = {!! json_encode($monthlyRequestsData['data'] ?? []) !!};
        
        const monthlyRequestsChart = new Chart(monthlyRequestsCtx, {
            type: 'bar',
            data: {
                labels: monthlyLabels,
                datasets: [{
                    label: 'Jumlah Permintaan',
                    data: monthlyData,
                    backgroundColor: 'rgba(59, 130, 246, 0.5)',
                    borderColor: 'rgba(59, 130, 246, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });

        const requestStatusCtx = document.getElementById('requestStatusChart').getContext('2d');
        
        // Data status dari controller
        const requestStatusData = {!! json_encode($requestStatusData ?? []) !!};
        
        const requestStatusChart = new Chart(requestStatusCtx, {
            type: 'doughnut',
            data: {
                labels: ['Pending', 'Disetujui', 'Ditolak', 'Diproses', 'Terkirim'],
                datasets: [{
                    data: [
                        requestStatusData['pending'] ? requestStatusData['pending']['count'] : 0,
                        requestStatusData['approved'] ? requestStatusData['approved']['count'] : 0,
                        requestStatusData['rejected'] ? requestStatusData['rejected']['count'] : 0,
                        requestStatusData['processing'] ? requestStatusData['processing']['count'] : 0,
                        requestStatusData['delivered'] ? requestStatusData['delivered']['count'] : 0
                    ],
                    backgroundColor: [
                        '#fbbf24',
                        '#10b981',
                        '#ef4444',
                        '#3b82f6',
                        '#8b5cf6'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.label || '';
                                let value = context.raw || 0;
                                let total = context.dataset.data.reduce((a, b) => a + b, 0);
                                let percentage = total > 0 ? Math.round((value / total) * 100) : 0;
                                return `${label}: ${value} (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });
    }
    
    // Report Functions
    function resetFilter() {
        document.getElementById('start_date').value = '{{ date("Y-m-01") }}';
        document.getElementById('end_date').value = '{{ date("Y-m-t") }}';
        document.getElementById('report_type').value = 'inventory';
    }
    
    function printReport() {
        // Sembunyikan elemen yang tidak perlu dicetak
        const elementsToHide = document.querySelectorAll('.sidebar, .topbar, .action-buttons, .filter-bar, .btn-group');
        elementsToHide.forEach(el => el.style.display = 'none');
        
        // Perlebar konten untuk cetak
        const tableCard = document.querySelector('.table-card');
        const originalStyles = {
            boxShadow: tableCard.style.boxShadow,
            padding: tableCard.style.padding
        };
        tableCard.style.boxShadow = 'none';
        tableCard.style.padding = '0';
        
        // Tambahkan judul cetak
        const printTitle = document.createElement('h4');
        printTitle.textContent = 'Laporan Sistem SILOG Polres';
        printTitle.style.textAlign = 'center';
        printTitle.style.marginBottom = '20px';
        printTitle.style.fontWeight = 'bold';
        tableCard.parentNode.insertBefore(printTitle, tableCard);
        
        // Tambahkan tanggal cetak
        const printDate = document.createElement('p');
        printDate.textContent = 'Tanggal: ' + new Date().toLocaleDateString('id-ID');
        printDate.style.textAlign = 'center';
        printDate.style.marginBottom = '20px';
        printDate.style.color = '#666';
        printTitle.parentNode.insertBefore(printDate, printTitle.nextSibling);
        
        // Cetak
        window.print();
        
        // Kembalikan tampilan normal setelah cetak
        setTimeout(() => {
            elementsToHide.forEach(el => el.style.display = '');
            tableCard.style.boxShadow = originalStyles.boxShadow;
            tableCard.style.padding = originalStyles.padding;
            if (printTitle.parentNode) {
                printTitle.parentNode.removeChild(printTitle);
            }
            if (printDate.parentNode) {
                printDate.parentNode.removeChild(printDate);
            }
        }, 500);
    }
    
    function viewReport(type) {
        const reportTitle = document.getElementById('reportTitle');
        const reportTable = document.getElementById('reportTable');
        const reportDetails = document.getElementById('reportDetails');
        
        // Set title
        reportTitle.textContent = `Detail Laporan ${getReportTypeName(type)}`;
        
        // Show loading
        reportTable.innerHTML = `
            <div class="text-center py-4">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2">Memuat data...</p>
            </div>
        `;
        
        // Show report details
        reportDetails.style.display = 'block';
        
        // Scroll to report details
        reportDetails.scrollIntoView({ behavior: 'smooth' });
        
        // Simulate AJAX call (in real implementation, you would fetch data from server)
        setTimeout(() => {
            reportTable.innerHTML = getReportTableHTML(type);
        }, 1000);
    }
    
    function hideReportDetails() {
        document.getElementById('reportDetails').style.display = 'none';
    }
    
    function exportReport(type) {
        // Tampilkan modal export
        $('#export_type').val(type);
        $('#generateReportModal').modal('show');
    }
    
    function downloadReport() {
        const type = $('#export_type').val();
        const format = $('#export_format').val();
        const startDate = $('#export_start_date').val();
        const endDate = $('#export_end_date').val();
        
        if (!type) {
            alert('Pilih jenis laporan terlebih dahulu!');
            return;
        }
        
        // Build download URL
        let url = '{{ route("admin.reports.export", ["type" => ":type"]) }}'.replace(':type', type);
        url += '?format=' + format;
        
        if (startDate) {
            url += '&start_date=' + startDate;
        }
        
        if (endDate) {
            url += '&end_date=' + endDate;
        }
        
        // Download file
        window.location.href = url;
        
        // Close modal
        $('#generateReportModal').modal('hide');
    }
    
    function getReportTypeName(type) {
        const types = {
            'inventory': 'Stok Barang',
            'requests': 'Permintaan Barang',
            'expenditures': 'Pengeluaran',
            'users': 'User',
            'satker': 'Satuan Kerja',
            'summary': 'Ringkasan'
        };
        return types[type] || type;
    }
    
    function getReportTableHTML(type) {
        const tables = {
            'inventory': `
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Kode Barang</th>
                            <th>Nama Barang</th>
                            <th>Kategori</th>
                            <th>Stok</th>
                            <th>Stok Minimal</th>
                            <th>Satuan</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>BRG-001</td>
                            <td>Komputer</td>
                            <td>Elektronik</td>
                            <td>15</td>
                            <td>5</td>
                            <td>Unit</td>
                            <td><span class="badge bg-success">Baik</span></td>
                        </tr>
                        <!-- More rows would be loaded from server -->
                    </tbody>
                </table>
            `,
            'requests': `
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Kode Permintaan</th>
                            <th>Pemohon</th>
                            <th>Barang</th>
                            <th>Jumlah</th>
                            <th>Satker</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>PMT-001</td>
                            <td>{{ $user->name }}</td>
                            <td>Komputer</td>
                            <td>2</td>
                            <td>Satker Utama</td>
                            <td><span class="badge badge-approved">Disetujui</span></td>
                            <td>15/10/2023</td>
                        </tr>
                        <!-- More rows would be loaded from server -->
                    </tbody>
                </table>
            `
        };
        
        return tables[type] || `
            <div class="alert alert-info">
                <i class="bi bi-info-circle me-2"></i>
                Data laporan ${getReportTypeName(type)} akan ditampilkan di sini.
                <br><small>Pada implementasi sebenarnya, data akan diambil dari server.</small>
            </div>
        `;
    }
    
    // Logout confirmation
    document.querySelector('form[action="{{ route("logout") }}"]').addEventListener('submit', function(e) {
        if (!confirm('Apakah Anda yakin ingin logout?')) {
            e.preventDefault();
        }
    });
</script>
</body>
</html>