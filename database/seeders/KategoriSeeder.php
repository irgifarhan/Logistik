<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Kategori;

class KategoriSeeder extends Seeder
{
    public function run(): void
    {
        $kategoris = [
            ['kode_kategori' => 'KTG-001', 'nama_kategori' => 'Alat Tulis Kantor', 'deskripsi' => 'Semua peralatan tulis menulis untuk kebutuhan kantor'],
            ['kode_kategori' => 'KTG-002', 'nama_kategori' => 'Elektronik', 'deskripsi' => 'Perangkat elektronik dan aksesorisnya'],
            ['kode_kategori' => 'KTG-003', 'nama_kategori' => 'Furniture', 'deskripsi' => 'Perabotan dan perlengkapan kantor'],
            ['kode_kategori' => 'KTG-004', 'nama_kategori' => 'Kendaraan', 'deskripsi' => 'Kendaraan dinas dan perlengkapannya'],
            ['kode_kategori' => 'KTG-005', 'nama_kategori' => 'Komputer & IT', 'deskripsi' => 'Perangkat komputer, jaringan, dan IT'],
            ['kode_kategori' => 'KTG-006', 'nama_kategori' => 'Perlengkapan Kebersihan', 'deskripsi' => 'Alat dan bahan kebersihan kantor'],
            ['kode_kategori' => 'KTG-007', 'nama_kategori' => 'Konsumsi', 'deskripsi' => 'Bahan makanan dan minuman'],
            ['kode_kategori' => 'KTG-008', 'nama_kategori' => 'Pakaian Dinas', 'deskripsi' => 'Seragam dan atribut dinas'],
            ['kode_kategori' => 'KTG-009', 'nama_kategori' => 'Perlengkapan Keamanan', 'deskripsi' => 'Alat keamanan dan perlindungan diri'],
            ['kode_kategori' => 'KTG-010', 'nama_kategori' => 'Perlengkapan Komunikasi', 'deskripsi' => 'Alat komunikasi dan aksesorisnya'],
            ['kode_kategori' => 'KTG-011', 'nama_kategori' => 'Alat Olahraga', 'deskripsi' => 'Perlengkapan olahraga dan rekreasi'],
            ['kode_kategori' => 'KTG-012', 'nama_kategori' => 'Bahan Bangunan', 'deskripsi' => 'Material dan perlengkapan bangunan'],
            ['kode_kategori' => 'KTG-013', 'nama_kategori' => 'Dokumen & Arsip', 'deskripsi' => 'Bahan dan perlengkapan pengarsipan'],
            ['kode_kategori' => 'KTG-014', 'nama_kategori' => 'Perlengkapan Medis', 'deskripsi' => 'Alat kesehatan dan P3K'],
            ['kode_kategori' => 'KTG-015', 'nama_kategori' => 'Lain-lain', 'deskripsi' => 'Kategori lain yang tidak termasuk dalam kategori di atas'],
        ];

        foreach ($kategoris as $kategori) {
            Kategori::create($kategori);
        }

        $this->command->info('Data kategori berhasil ditambahkan!');
    }
}