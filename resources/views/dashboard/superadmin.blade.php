<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard SuperAdmin - Manajemen User | SILOG Polres</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --primary: #1e3a8a;
            --primary-light: #3b82f6;
            --secondary: #dc2626;
            --success: #10b981;
            --warning: #f59e0b;
            --info: #0ea5e9;
            --purple: #8b5cf6;
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
            background: linear-gradient(135deg, var(--purple) 0%, #a855f7 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 1.2rem;
        }
        
        .superadmin-badge {
            background: linear-gradient(135deg, var(--purple) 0%, #a855f7 100%);
            color: white;
            padding: 0.2rem 0.8rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
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
        
        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .stat-card {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            transition: transform 0.3s;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
        }
        
        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            margin-bottom: 1rem;
        }
        
        .stat-content h3 {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        
        .stat-content p {
            color: #64748b;
            font-size: 0.9rem;
        }
        
        /* User Management Cards */
        .user-management-header {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            margin-bottom: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .search-box {
            position: relative;
            width: 300px;
        }
        
        .search-box input {
            width: 100%;
            padding: 0.6rem 2.5rem 0.6rem 1rem;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            font-size: 0.9rem;
        }
        
        .search-box i {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #64748b;
        }
        
        /* Filter Tabs */
        .filter-tabs {
            display: flex;
            gap: 0.5rem;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
        }
        
        .filter-tab {
            padding: 0.6rem 1.2rem;
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            text-decoration: none;
            color: var(--dark);
            font-weight: 500;
            transition: all 0.3s;
        }
        
        .filter-tab:hover, .filter-tab.active {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
        }
        
        /* User Table */
        .table-card {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            margin-bottom: 2rem;
        }
        
        .table-card h5 {
            margin-bottom: 1.5rem;
            color: var(--dark);
            font-weight: 600;
        }
        
        .table-responsive {
            border-radius: 8px;
            overflow: hidden;
        }
        
        .table th {
            background-color: #f8fafc;
            font-weight: 600;
            color: var(--dark);
            border-bottom: 2px solid #e2e8f0;
        }
        
        /* Badges */
        .badge {
            padding: 0.4rem 0.8rem;
            font-weight: 500;
        }
        
        .badge-admin {
            background-color: #dbeafe;
            color: #1e40af;
        }
        
        .badge-superadmin {
            background-color: #ede9fe;
            color: #7c3aed;
        }
        
        .badge-user {
            background-color: #dcfce7;
            color: #15803d;
        }
        
        .badge-inactive {
            background-color: #f3f4f6;
            color: #6b7280;
        }
        
        .badge-active {
            background-color: #d1fae5;
            color: #065f46;
        }
        
        /* Action Buttons */
        .action-buttons {
            display: flex;
            gap: 0.5rem;
        }
        
        .btn-action {
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 6px;
            border: none;
            transition: all 0.3s;
        }
        
        .btn-edit {
            background-color: #dbeafe;
            color: #1e40af;
        }
        
        .btn-edit:hover {
            background-color: #bfdbfe;
        }
        
        .btn-delete {
            background-color: #fee2e2;
            color: #dc2626;
        }
        
        .btn-delete:hover {
            background-color: #fecaca;
        }
        
        .btn-reset {
            background-color: #fef3c7;
            color: #d97706;
        }
        
        .btn-reset:hover {
            background-color: #fde68a;
        }
        
        /* Modal Styles */
        .modal-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            color: white;
        }
        
        /* Quick Actions */
        .quick-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }
        
        .action-btn {
            background: white;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            padding: 1.5rem 1rem;
            text-align: center;
            text-decoration: none;
            color: var(--dark);
            transition: all 0.3s;
        }
        
        .action-btn:hover {
            border-color: var(--primary);
            background-color: #f8fafc;
            transform: translateY(-3px);
        }
        
        .action-icon {
            font-size: 2rem;
            margin-bottom: 0.8rem;
            color: var(--primary);
        }
        
        /* Role Distribution Chart */
        .chart-card {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            margin-bottom: 2rem;
        }
        
        .chart-card h5 {
            margin-bottom: 1.5rem;
            color: var(--dark);
            font-weight: 600;
        }
        
        /* Alert */
        .alert-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            min-width: 300px;
        }
        
        /* Pagination */
        .pagination-container {
            display: flex;
            justify-content: center;
            margin-top: 1.5rem;
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
            
            .search-box {
                width: 100%;
                margin-top: 1rem;
            }
            
            .user-management-header {
                flex-direction: column;
                align-items: flex-start;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-brand">
            <h3>SILOG</h3>
            <p>SuperAdmin Dashboard</p>
        </div>
        
        <div class="sidebar-nav">
            <div class="nav-item">
                <a href="{{ route('admin.dashboard') }}" class="nav-link">
                    <i class="bi bi-speedometer2"></i>
                    <span>Dashboard Utama</span>
                </a>
            </div>
            
            <div class="nav-item">
                <a href="{{ route('superadmin.users') }}" class="nav-link active">
                    <i class="bi bi-people-fill"></i>
                    <span>Manajemen User</span>
                </a>
            </div>
            
            <div class="nav-item">
                <a href="#" class="nav-link">
                    <i class="bi bi-shield-check"></i>
                    <span>Manajemen Role</span>
                </a>
            </div>
            
            <div class="nav-item">
                <a href="#" class="nav-link">
                    <i class="bi bi-box-seam"></i>
                    <span>Manajemen Barang</span>
                </a>
            </div>
            
            <div class="nav-item">
                <a href="#" class="nav-link">
                    <i class="bi bi-clipboard-check"></i>
                    <span>Permintaan Barang</span>
                </a>
            </div>
            
            <div class="nav-item">
                <a href="#" class="nav-link">
                    <i class="bi bi-building"></i>
                    <span>Satuan Kerja</span>
                </a>
            </div>
            
            <div class="nav-item">
                <a href="#" class="nav-link">
                    <i class="bi bi-file-text"></i>
                    <span>Laporan Sistem</span>
                </a>
            </div>
            
            <div class="nav-item">
                <a href="#" class="nav-link">
                    <i class="bi bi-gear"></i>
                    <span>Pengaturan Sistem</span>
                </a>
            </div>
            
            <div class="nav-item">
                <a href="#" class="nav-link">
                    <i class="bi bi-clock-history"></i>
                    <span>Aktivitas Sistem</span>
                </a>
            </div>
        </div>
        
        <div class="sidebar-footer" style="padding: 1.5rem; position: absolute; bottom: 0; width: 100%;">
            <div class="text-center">
                <small style="opacity: 0.7;">Sistem Logistik Polres</small><br>
                <small style="opacity: 0.5;">SuperAdmin Mode</small>
            </div>
        </div>
    </div>
    
    <!-- Main Content -->
    <div class="main-content">
        <!-- Top Bar -->
        <div class="topbar">
            <div>
                <h4 class="mb-0">Manajemen User</h4>
                <small class="text-muted">Kelola pengguna sistem SILOG</small>
            </div>
            <div class="user-info">
                <div class="user-avatar">
                    {{ substr($user->name, 0, 1) }}
                </div>
                <div>
                    <strong>{{ $user->name }}</strong><br>
                    <span class="superadmin-badge">SUPERADMIN</span>
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
        
        <!-- Quick Actions -->
        <div class="quick-actions">
            <a href="#" class="action-btn" data-bs-toggle="modal" data-bs-target="#addUserModal">
                <div class="action-icon">
                    <i class="bi bi-person-plus"></i>
                </div>
                <div>Tambah User Baru</div>
            </a>
            
            <a href="#" class="action-btn">
                <div class="action-icon">
                    <i class="bi bi-file-earmark-excel"></i>
                </div>
                <div>Export Data</div>
            </a>
            
            <a href="#" class="action-btn">
                <div class="action-icon">
                    <i class="bi bi-file-earmark-text"></i>
                </div>
                <div>Laporan User</div>
            </a>
            
            <a href="#" class="action-btn">
                <div class="action-icon">
                    <i class="bi bi-shield-check"></i>
                </div>
                <div>Manajemen Role</div>
            </a>
        </div>
        
        <!-- Stats Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon" style="background-color: #ede9fe; color: #8b5cf6;">
                    <i class="bi bi-people"></i>
                </div>
                <div class="stat-content">
                    <h3>{{ $stats['total_users'] ?? 0 }}</h3>
                    <p>Total Pengguna</p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon" style="background-color: #dbeafe; color: #3b82f6;">
                    <i class="bi bi-shield-check"></i>
                </div>
                <div class="stat-content">
                    <h3>{{ $stats['admin_users'] ?? 0 }}</h3>
                    <p>Admin</p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon" style="background-color: #dcfce7; color: #10b981;">
                    <i class="bi bi-person-check"></i>
                </div>
                <div class="stat-content">
                    <h3>{{ $stats['active_users'] ?? 0 }}</h3>
                    <p>User Aktif</p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon" style="background-color: #f3f4f6; color: #6b7280;">
                    <i class="bi bi-person-x"></i>
                </div>
                <div class="stat-content">
                    <h3>{{ $stats['inactive_users'] ?? 0 }}</h3>
                    <p>User Non-Aktif</p>
                </div>
            </div>
        </div>
        
        <!-- User Management Header -->
        <div class="user-management-header">
            <div>
                <h5 class="mb-1">Daftar Pengguna Sistem</h5>
                <small class="text-muted">Total {{ $stats['total_users'] ?? 0 }} pengguna terdaftar</small>
            </div>
            <div class="search-box">
                <input type="text" placeholder="Cari user berdasarkan nama atau NRP...">
                <i class="bi bi-search"></i>
            </div>
        </div>
        
        <!-- Filter Tabs -->
        <div class="filter-tabs">
            <a href="?filter=all" class="filter-tab {{ request('filter') == 'all' || !request('filter') ? 'active' : '' }}">Semua</a>
            <a href="?filter=superadmin" class="filter-tab {{ request('filter') == 'superadmin' ? 'active' : '' }}">SuperAdmin</a>
            <a href="?filter=admin" class="filter-tab {{ request('filter') == 'admin' ? 'active' : '' }}">Admin</a>
            <a href="?filter=user" class="filter-tab {{ request('filter') == 'user' ? 'active' : '' }}">User Biasa</a>
            <a href="?filter=active" class="filter-tab {{ request('filter') == 'active' ? 'active' : '' }}">Aktif</a>
            <a href="?filter=inactive" class="filter-tab {{ request('filter') == 'inactive' ? 'active' : '' }}">Non-Aktif</a>
        </div>
        
        <!-- Role Distribution Chart -->
        <div class="chart-card">
            <h5>Distribusi Role Pengguna</h5>
            <canvas id="roleChart" height="150"></canvas>
        </div>
        
        <!-- Users Table -->
        <div class="table-card">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0">Daftar User</h5>
                <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
                    <i class="bi bi-person-plus"></i> Tambah User
                </button>
            </div>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>NRP</th>
                            <th>Nama Lengkap</th>
                            <th>Email</th>
                            <th>Satker</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Terakhir Login</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $userItem)
                        <tr>
                            <td>{{ $loop->iteration + (($users->currentPage() - 1) * $users->perPage()) }}</td>
                            <td><strong>{{ $userItem->nrp ?? 'N/A' }}</strong></td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="user-avatar-small me-2" style="width: 30px; height: 30px; background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 0.9rem;">
                                        {{ substr($userItem->name, 0, 1) }}
                                    </div>
                                    {{ $userItem->name }}
                                </div>
                            </td>
                            <td>{{ $userItem->email }}</td>
                            <td>{{ $userItem->satker->nama_satker ?? 'Tidak ditentukan' }}</td>
                            <td>
                                @if($userItem->role == 'superadmin')
                                    <span class="badge badge-superadmin">SuperAdmin</span>
                                @elseif($userItem->role == 'admin')
                                    <span class="badge badge-admin">Admin</span>
                                @else
                                    <span class="badge badge-user">User</span>
                                @endif
                            </td>
                            <td>
                                @if($userItem->status == 'active')
                                    <span class="badge badge-active">Aktif</span>
                                @else
                                    <span class="badge badge-inactive">Non-Aktif</span>
                                @endif
                            </td>
                            <td>{{ $userItem->last_login_at ? $userItem->last_login_at->format('d/m/Y H:i') : 'Belum login' }}</td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn-action btn-edit" data-bs-toggle="modal" data-bs-target="#editUserModal" 
                                            data-user-id="{{ $userItem->id }}"
                                            data-user-name="{{ $userItem->name }}"
                                            data-user-email="{{ $userItem->email }}"
                                            data-user-nrp="{{ $userItem->nrp }}"
                                            data-user-role="{{ $userItem->role }}"
                                            data-user-status="{{ $userItem->status }}"
                                            data-user-satker="{{ $userItem->satker_id }}">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button class="btn-action btn-reset" data-bs-toggle="modal" data-bs-target="#resetPasswordModal" 
                                            data-user-id="{{ $userItem->id }}"
                                            data-user-name="{{ $userItem->name }}">
                                        <i class="bi bi-key"></i>
                                    </button>
                                    @if($userItem->id != auth()->id())
                                    <button class="btn-action btn-delete" data-bs-toggle="modal" data-bs-target="#deleteUserModal" 
                                            data-user-id="{{ $userItem->id }}"
                                            data-user-name="{{ $userItem->name }}">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-4">
                                <i class="bi bi-people" style="font-size: 2rem; color: #cbd5e1;"></i>
                                <p class="mt-2 text-muted">Belum ada user terdaftar</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            @if($users->hasPages())
            <div class="pagination-container">
                {{ $users->links() }}
            </div>
            @endif
        </div>
        
        <!-- Recent Activity -->
        <div class="table-card">
            <h5>Aktivitas User Terbaru</h5>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Aktivitas</th>
                            <th>Waktu</th>
                            <th>IP Address</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse(($activities ?? collect()) as $activity)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="user-avatar-small me-2" style="width: 30px; height: 30px; background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 0.9rem;">
                                        {{ substr($activity->user->name, 0, 1) }}
                                    </div>
                                    {{ $activity->user->name }}
                                </div>
                            </td>
                            <td>{{ $activity->description }}</td>
                            <td>{{ $activity->created_at->diffForHumans() }}</td>
                            <td><code>{{ $activity->ip_address }}</code></td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-4">
                                <i class="bi bi-clock-history" style="font-size: 2rem; color: #cbd5e1;"></i>
                                <p class="mt-2 text-muted">Belum ada aktivitas</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Add User Modal -->
    <div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addUserModalLabel">Tambah User Baru</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="{{ route('superadmin.users.store') }}">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Nama Lengkap</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="nrp" class="form-label">NRP</label>
                                <input type="text" class="form-control" id="nrp" name="nrp" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="role" class="form-label">Role</label>
                                <select class="form-select" id="role" name="role" required>
                                    <option value="user">User</option>
                                    <option value="admin">Admin</option>
                                    <option value="superadmin">SuperAdmin</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="satker_id" class="form-label">Satuan Kerja</label>
                                <select class="form-select" id="satker_id" name="satker_id" required>
                                    <option value="">Pilih Satker</option>
                                    @foreach($satkers as $satker)
                                        <option value="{{ $satker->id }}">{{ $satker->nama_satker }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" id="status" name="status" required>
                                    <option value="active">Aktif</option>
                                    <option value="inactive">Non-Aktif</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan User</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Edit User Modal -->
    <div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editUserModalLabel">Edit User</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" id="editUserForm">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="edit_name" class="form-label">Nama Lengkap</label>
                                <input type="text" class="form-control" id="edit_name" name="name" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="edit_nrp" class="form-label">NRP</label>
                                <input type="text" class="form-control" id="edit_nrp" name="nrp" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="edit_email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="edit_email" name="email" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="edit_role" class="form-label">Role</label>
                                <select class="form-select" id="edit_role" name="role" required>
                                    <option value="user">User</option>
                                    <option value="admin">Admin</option>
                                    <option value="superadmin">SuperAdmin</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="edit_satker_id" class="form-label">Satuan Kerja</label>
                                <select class="form-select" id="edit_satker_id" name="satker_id" required>
                                    <option value="">Pilih Satker</option>
                                    @foreach($satkers as $satker)
                                        <option value="{{ $satker->id }}">{{ $satker->nama_satker }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="edit_status" class="form-label">Status</label>
                                <select class="form-select" id="edit_status" name="status" required>
                                    <option value="active">Aktif</option>
                                    <option value="inactive">Non-Aktif</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Update User</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Reset Password Modal -->
    <div class="modal fade" id="resetPasswordModal" tabindex="-1" aria-labelledby="resetPasswordModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="resetPasswordModalLabel">Reset Password</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            