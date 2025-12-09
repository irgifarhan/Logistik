<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Barang;
use App\Models\Kategori;
use App\Models\Satuan;
use App\Models\Gudang;

class BarangSeeder extends Seeder
{
    public function run(): void
    {
        $kategoris = Kategori::all();
        $satuans = Satuan::all();
        $gudangs = Gudang::all();
        
        if ($kategoris->count() == 0 || $satuans->count() == 0 || $gudangs->count() == 0) {
            $this->command->error('Harap jalankan KategoriSeeder, SatuanSeeder, dan GudangSeeder terlebih dahulu!');
            return;
        }

        $barangs = [
            [
                'kode_barang' => 'BRG-001',
                'nama_barang' => 'Bola Lampu LED 10W',
                'kategori_id' => $kategoris->where('nama_kategori', 'Elektronik')->first()->id,
                'satuan_id' => $satuans->where('nama_satuan', 'Pcs')->first()->id,
                'gudang_id' => $gudangs->where('nama_gudang', 'Gudang Elektronik')->first()->id,
                'stok' => 100,
                'stok_minimal' => 20,
                'lokasi' => 'Rak A1',
                'keterangan' => 'Lampu LED putih 10 watt',
            ],
            [
                'kode_barang' => 'BRG-002',
                'nama_barang' => 'Kertas A4 80gr',
                'kategori_id' => $kategoris->where('nama_kategori', 'Alat Tulis Kantor')->first()->id,
                'satuan_id' => $satuans->where('nama_satuan', 'Rim')->first()->id,
                'gudang_id' => $gudangs->where('nama_gudang', 'Gudang ATK')->first()->id,
                'stok' => 50,
                'stok_minimal' => 10,
                'lokasi' => 'Rak B2',
                'keterangan' => 'Kertas HVS A4 80 gram',
            ],
            [
                'kode_barang' => 'BRG-003',
                'nama_barang' => 'Meja Kantor',
                'kategori_id' => $kategoris->where('nama_kategori', 'Furniture')->first()->id,
                'satuan_id' => $satuans->where('nama_satuan', 'Unit')->first()->id,
                'gudang_id' => $gudangs->where('nama_gudang', 'Gudang Furniture')->first()->id,
                'stok' => 15,
                'stok_minimal' => 5,
                'lokasi' => 'Area Furniture',
                'keterangan' => 'Meja kantor standar ukuran 120x60 cm',
            ],
            [
                'kode_barang' => 'BRG-004',
                'nama_barang' => 'Air Mineral Galon',
                'kategori_id' => $kategoris->where('nama_kategori', 'Konsumsi')->first()->id,
                'satuan_id' => $satuans->where('nama_satuan', 'Botol')->first()->id,
                'gudang_id' => $gudangs->where('nama_gudang', 'Gudang Konsumsi')->first()->id,
                'stok' => 30,
                'stok_minimal' => 10,
                'lokasi' => 'Rak Minuman',
                'keterangan' => 'Air mineral kemasan galon 19 liter',
            ],
            [
                'kode_barang' => 'BRG-005',
                'nama_barang' => 'Laptop Dell Latitude',
                'kategori_id' => $kategoris->where('nama_kategori', 'Komputer & IT')->first()->id,
                'satuan_id' => $satuans->where('nama_satuan', 'Unit')->first()->id,
                'gudang_id' => $gudangs->where('nama_gudang', 'Gudang Elektronik')->first()->id,
                'stok' => 8,
                'stok_minimal' => 3,
                'lokasi' => 'Lemari Elektronik',
                'keterangan' => 'Laptop Dell Latitude i5, 8GB RAM, 256GB SSD',
            ],
            [
                'kode_barang' => 'BRG-006',
                'nama_barang' => 'Sapu Lantai',
                'kategori_id' => $kategoris->where('nama_kategori', 'Perlengkapan Kebersihan')->first()->id,
                'satuan_id' => $satuans->where('nama_satuan', 'Pcs')->first()->id,
                'gudang_id' => $gudangs->where('nama_gudang', 'Gudang Utama')->first()->id,
                'stok' => 25,
                'stok_minimal' => 10,
                'lokasi' => 'Rak Kebersihan',
                'keterangan' => 'Sapu lantai plastik',
            ],
            [
                'kode_barang' => 'BRG-007',
                'nama_barang' => 'Seragam Dinas Harian',
                'kategori_id' => $kategoris->where('nama_kategori', 'Pakaian Dinas')->first()->id,
                'satuan_id' => $satuans->where('nama_satuan', 'Set')->first()->id,
                'gudang_id' => $gudangs->where('nama_gudang', 'Gudang Utama')->first()->id,
                'stok' => 40,
                'stok_minimal' => 15,
                'lokasi' => 'Lemari Seragam',
                'keterangan' => 'Seragam dinas harian ukuran L',
            ],
            [
                'kode_barang' => 'BRG-008',
                'nama_barang' => 'Walkie Talkie',
                'kategori_id' => $kategoris->where('nama_kategori', 'Perlengkapan Komunikasi')->first()->id,
                'satuan_id' => $satuans->where('nama_satuan', 'Unit')->first()->id,
                'gudang_id' => $gudangs->where('nama_gudang', 'Gudang Elektronik')->first()->id,
                'stok' => 12,
                'stok_minimal' => 5,
                'lokasi' => 'Rak Komunikasi',
                'keterangan' => 'Walkie talkie UHF 5 watt',
            ],
            [
                'kode_barang' => 'BRG-009',
                'nama_barang' => 'Paku Beton',
                'kategori_id' => $kategoris->where('nama_kategori', 'Bahan Bangunan')->first()->id,
                'satuan_id' => $satuans->where('nama_satuan', 'Kg')->first()->id,
                'gudang_id' => $gudangs->where('nama_gudang', 'Gudang Utama')->first()->id,
                'stok' => 50,
                'stok_minimal' => 20,
                'lokasi' => 'Rak Bangunan',
                'keterangan' => 'Paku beton ukuran 3 inch',
            ],
            [
                'kode_barang' => 'BRG-010',
                'nama_barang' => 'Bola Basket',
                'kategori_id' => $kategoris->where('nama_kategori', 'Alat Olahraga')->first()->id,
                'satuan_id' => $satuans->where('nama_satuan', 'Buah')->first()->id,
                'gudang_id' => $gudangs->where('nama_gudang', 'Gudang Utama')->first()->id,
                'stok' => 10,
                'stok_minimal' => 3,
                'lokasi' => 'Rak Olahraga',
                'keterangan' => 'Bola basket ukuran 7',
            ],
        ];

        foreach ($barangs as $barang) {
            Barang::create($barang);
        }

        $this->command->info('Data barang berhasil ditambahkan!');
    }
}