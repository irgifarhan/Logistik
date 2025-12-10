<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Satker;

class SatkerSeeder extends Seeder
{
    public function run(): void
    {
        $satkers = [
            [
                'kode_satker' => 'POL-001',
                'nama_satker' => 'POLRES METRO JAKARTA PUSAT',
                'alamat' => 'Jl. Kramat Raya No. 75, Jakarta Pusat',
                'telepon' => '(021) 3500001',
                'email' => 'polresjaktim@polri.go.id',
                'nama_kepala' => 'AKBP Dr. Ahmad Syafii, S.H., M.H.',
                'pangkat_kepala' => 'AKBP',
                'nrp_kepala' => '72010101',
            ],
            [
                'kode_satker' => 'POL-002',
                'nama_satker' => 'SATUAN LALU LINTAS',
                'alamat' => 'Jl. Kramat Raya No. 75, Jakarta Pusat',
                'telepon' => '(021) 3500002',
                'email' => 'satlantaspolres@polri.go.id',
                'nama_kepala' => 'KOMPOL Budi Santoso, S.H.',
                'pangkat_kepala' => 'KOMPOL',
                'nrp_kepala' => '72010202',
            ],
            [
                'kode_satker' => 'POL-003',
                'nama_satker' => 'SATUAN RESERSE KRIMINAL',
                'alamat' => 'Jl. Kramat Raya No. 75, Jakarta Pusat',
                'telepon' => '(021) 3500003',
                'email' => 'reskrimpolres@polri.go.id',
                'nama_kepala' => 'KOMPOL Dr. Siti Rahayu, S.H., M.H.',
                'pangkat_kepala' => 'KOMPOL',
                'nrp_kepala' => '72010303',
            ],
            [
                'kode_satker' => 'POL-004',
                'nama_satker' => 'SATUAN SAMAPTA',
                'alamat' => 'Jl. Kramat Raya No. 75, Jakarta Pusat',
                'telepon' => '(021) 3500004',
                'email' => 'samaptapolres@polri.go.id',
                'nama_kepala' => 'KOMPOL Ahmad Fauzi, S.I.K.',
                'pangkat_kepala' => 'KOMPOL',
                'nrp_kepala' => '72010404',
            ],
            [
                'kode_satker' => 'POL-005',
                'nama_satker' => 'SATUAN BINA MASYARAKAT',
                'alamat' => 'Jl. Kramat Raya No. 75, Jakarta Pusat',
                'telepon' => '(021) 3500005',
                'email' => 'binmaspolres@polri.go.id',
                'nama_kepala' => 'KOMPOL Dewi Lestari, S.Pd.',
                'pangkat_kepala' => 'KOMPOL',
                'nrp_kepala' => '72010505',
            ],
            [
                'kode_satker' => 'POL-006',
                'nama_satker' => 'SATUAN INTELIJEN',
                'alamat' => 'Jl. Kramat Raya No. 75, Jakarta Pusat',
                'telepon' => '(021) 3500006',
                'email' => 'intelpolres@polri.go.id',
                'nama_kepala' => 'KOMPOL Rudi Hartono, S.H.',
                'pangkat_kepala' => 'KOMPOL',
                'nrp_kepala' => '72010606',
            ],
            [
                'kode_satker' => 'POL-007',
                'nama_satker' => 'SATUAN TAHANAN',
                'alamat' => 'Jl. Kramat Raya No. 75, Jakarta Pusat',
                'telepon' => '(021) 3500007',
                'email' => 'tahananpolres@polri.go.id',
                'nama_kepala' => 'AKP Eko Prasetyo, S.H.',
                'pangkat_kepala' => 'AKP',
                'nrp_kepala' => '72010707',
            ],
            [
                'kode_satker' => 'POL-008',
                'nama_satker' => 'SATUAN LOGISTIK',
                'alamat' => 'Jl. Kramat Raya No. 75, Jakarta Pusat',
                'telepon' => '(021) 3500008',
                'email' => 'logistikpolres@polri.go.id',
                'nama_kepala' => 'KOMPOL Fitriani, S.E.',
                'pangkat_kepala' => 'KOMPOL',
                'nrp_kepala' => '72010808',
            ],
            [
                'kode_satker' => 'POL-009',
                'nama_satker' => 'SATUAN KEPEGAWAIAN',
                'alamat' => 'Jl. Kramat Raya No. 75, Jakarta Pusat',
                'telepon' => '(021) 3500009',
                'email' => 'kepegawaianpolres@polri.go.id',
                'nama_kepala' => 'KOMPOL Bambang Sutrisno, S.H.',
                'pangkat_kepala' => 'KOMPOL',
                'nrp_kepala' => '72010909',
            ],
            [
                'kode_satker' => 'POL-010',
                'nama_satker' => 'SATUAN KEUANGAN',
                'alamat' => 'Jl. Kramat Raya No. 75, Jakarta Pusat',
                'telepon' => '(021) 3500010',
                'email' => 'keuanganpolres@polri.go.id',
                'nama_kepala' => 'KOMPOL Sri Wahyuni, S.E., M.Ak.',
                'pangkat_kepala' => 'KOMPOL',
                'nrp_kepala' => '72011010',
            ],
            [
                'kode_satker' => 'POL-011',
                'nama_satker' => 'SATUAN BARANG MILIK NEGARA',
                'alamat' => 'Jl. Kramat Raya No. 75, Jakarta Pusat',
                'telepon' => '(021) 3500011',
                'email' => 'bmnpolres@polri.go.id',
                'nama_kepala' => 'KOMPOL Agus Setiawan, S.T.',
                'pangkat_kepala' => 'KOMPOL',
                'nrp_kepala' => '72011111',
            ],
            [
                'kode_satker' => 'POL-012',
                'nama_satker' => 'SATUAN KESEHATAN',
                'alamat' => 'Jl. Kramat Raya No. 75, Jakarta Pusat',
                'telepon' => '(021) 3500012',
                'email' => 'kesehatanpolres@polri.go.id',
                'nama_kepala' => 'KOMPOL dr. Rina Anggraeni, Sp.PD',
                'pangkat_kepala' => 'KOMPOL',
                'nrp_kepala' => '72011212',
            ],
            [
                'kode_satker' => 'POL-013',
                'nama_satker' => 'SATUAN HUMAS',
                'alamat' => 'Jl. Kramat Raya No. 75, Jakarta Pusat',
                'telepon' => '(021) 3500013',
                'email' => 'humaspolres@polri.go.id',
                'nama_kepala' => 'KOMPOL Dwi Cahyono, S.Sos.',
                'pangkat_kepala' => 'KOMPOL',
                'nrp_kepala' => '72011313',
            ],
            [
                'kode_satker' => 'POL-014',
                'nama_satker' => 'SATUAN TEKNOLOGI INFORMASI',
                'alamat' => 'Jl. Kramat Raya No. 75, Jakarta Pusat',
                'telepon' => '(021) 3500014',
                'email' => 'tipolres@polri.go.id',
                'nama_kepala' => 'KOMPOL Yudi Hermawan, S.Kom.',
                'pangkat_kepala' => 'KOMPOL',
                'nrp_kepala' => '72011414',
            ],
            [
                'kode_satker' => 'POL-015',
                'nama_satker' => 'SATUAN PAMOBVIT',
                'alamat' => 'Jl. Kramat Raya No. 75, Jakarta Pusat',
                'telepon' => '(021) 3500015',
                'email' => 'pamobvitpolres@polri.go.id',
                'nama_kepala' => 'KOMPOL Andi Wijaya, S.H.',
                'pangkat_kepala' => 'KOMPOL',
                'nrp_kepala' => '72011515',
            ],
        ];

        foreach ($satkers as $satker) {
            Satker::create($satker);
        }

        $this->command->info('Data satker berhasil ditambahkan!');
        $this->command->info('Total satker: ' . count($satkers));
    }
}