<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permintaan;
use App\Models\Pengeluaran;
use App\Models\Barang;
use App\Models\User;
use App\Models\Satker;
use App\Models\Gudang;

class DummyDataSeeder extends Seeder
{
    public function run()
    {
        // Ambil data untuk dummy
        $barangs = Barang::all();
        $users = User::where('role', 'user')->get();
        $satkers = Satker::all();
        $gudangs = Gudang::all();
        $admins = User::whereIn('role', ['admin', 'superadmin'])->get();

        if ($barangs->isEmpty() || $users->isEmpty() || $satkers->isEmpty()) {
            $this->command->info('Cannot create dummy data. Master data not found!');
            return;
        }

        // ==================== PERMINTAAN ====================
        $statuses = ['pending', 'approved', 'rejected'];
        $keteranganOptions = [
            'Untuk kebutuhan patroli',
            'Pengganti barang rusak',
            'Stok habis',
            'Kebutuhan mendesak',
            'Permintaan rutin',
        ];

        for ($i = 1; $i <= 20; $i++) {
            $status = $statuses[array_rand($statuses)];
            $user = $users->random();
            $barang = $barangs->random();
            $satker = $satkers->random();
            
            $permintaan = Permintaan::create([
                'kode_permintaan' => 'PMT-' . str_pad($i, 6, '0', STR_PAD_LEFT),
                'user_id' => $user->id,
                'barang_id' => $barang->id,
                'satker_id' => $satker->id,
                'jumlah' => rand(1, 50),
                'keterangan' => $keteranganOptions[array_rand($keteranganOptions)],
                'status' => $status,
                'approved_by' => $status !== 'pending' ? $admins->random()->id : null,
                'approved_at' => $status !== 'pending' ? now()->subDays(rand(1, 10)) : null,
                'alasan_penolakan' => $status === 'rejected' ? 'Stok tidak mencukupi' : null,
                'created_at' => now()->subDays(rand(1, 30)),
            ]);

            // ==================== PENGELUARAN ====================
            if ($status === 'approved' && rand(0, 1)) {
                $penerima = $users->random();
                $gudang = $gudangs->random();
                
                Pengeluaran::create([
                    'kode_pengeluaran' => 'PGL-' . str_pad($i, 6, '0', STR_PAD_LEFT),
                    'permintaan_id' => $permintaan->id,
                    'barang_id' => $barang->id,
                    'jumlah' => $permintaan->jumlah,
                    'gudang_id' => $gudang->id,
                    'satker_id' => $satker->id,
                    'penerima_id' => $penerima->id,
                    'keterangan' => 'Pengeluaran berdasarkan permintaan ' . $permintaan->kode_permintaan,
                    'created_at' => $permintaan->approved_at->addDays(rand(1, 3)),
                ]);

                // Kurangi stok barang
                $barang->decrement('stok', $permintaan->jumlah);
            }
        }

        $this->command->info('Dummy data created:');
        $this->command->info('- ' . Permintaan::count() . ' permintaan');
        $this->command->info('- ' . Pengeluaran::count() . ' pengeluaran');
        $this->command->info('- ' . Barang::where('stok', '<=', \DB::raw('stok_minimal'))->count() . ' barang stok rendah');
    }
}