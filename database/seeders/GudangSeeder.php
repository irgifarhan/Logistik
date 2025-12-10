<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Gudang;
use App\Models\Satker;

class GudangSeeder extends Seeder
{
    public function run(): void
    {
        $satkers = Satker::all();
        
        if ($satkers->count() == 0) {
            $this->command->error('Harap jalankan SatkerSeeder terlebih dahulu!');
            return;
        }

        $gudangs = [
            [
                'kode_gudang' => 'G001',
                'nama_gudang' => 'Gudang Utama',
                'satker_id' => $satkers->first()->id,
                'lokasi' => 'Gedung A Lantai 1',
                'penanggung_jawab' => 'Budi Santoso',
                'telepon' => '021-1111111',
                'keterangan' => 'Gudang utama penyimpanan barang',
            ],
            [
                'kode_gudang' => 'G002',
                'nama_gudang' => 'Gudang Elektronik',
                'satker_id' => $satkers->first()->id,
                'lokasi' => 'Gedung B Lantai 2',
                'penanggung_jawab' => 'Siti Rahayu',
                'telepon' => '021-2222222',
                'keterangan' => 'Khusus barang elektronik',
            ],
            [
                'kode_gudang' => 'G003',
                'nama_gudang' => 'Gudang ATK',
                'satker_id' => $satkers->skip(1)->first()->id,
                'lokasi' => 'Gedung C Lantai 1',
                'penanggung_jawab' => 'Ahmad Fauzi',
                'telepon' => '021-3333333',
                'keterangan' => 'Khusus alat tulis kantor',
            ],
            [
                'kode_gudang' => 'G004',
                'nama_gudang' => 'Gudang Furniture',
                'satker_id' => $satkers->skip(2)->first()->id,
                'lokasi' => 'Gedung D Lantai 1',
                'penanggung_jawab' => 'Dewi Lestari',
                'telepon' => '021-4444444',
                'keterangan' => 'Khusus perabotan kantor',
            ],
            [
                'kode_gudang' => 'G005',
                'nama_gudang' => 'Gudang Konsumsi',
                'satker_id' => $satkers->skip(3)->first()->id,
                'lokasi' => 'Gedung E Lantai 1',
                'penanggung_jawab' => 'Rudi Hartono',
                'telepon' => '021-5555555',
                'keterangan' => 'Khusus bahan makanan dan minuman',
            ],
        ];

        foreach ($gudangs as $gudang) {
            Gudang::create($gudang);
        }

        $this->command->info('Data gudang berhasil ditambahkan!');
    }
}