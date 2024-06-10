<?php

namespace App\Http\Controllers;

use App\Models\asset;
use App\Models\refDivisi;
use App\Models\refKelasAset;
use App\Models\refKodeProjek;
use App\Models\refLokasi;
use App\Models\refRole;
use App\Models\transaksiAssetKeluar;
use App\Models\transaksiAssetMasuk;
use App\Models\User;

class dashboardController extends Controller
{
    public function getAllCounts()
    {
        $counts = [
            'role_count' => refRole::count(),
            'user_count' => User::count(),
            'lokasi_count' => refLokasi::count(),
            'kelas_aset_count' => refKelasAset::count(),
            'kode_projek_count' => refKodeProjek::count(),
            'divisi_count' => refDivisi::count(),
            'asset_count' => asset::count(),
            'asset_masuk_count' => transaksiAssetMasuk::count(),
            'asset_keluar_count' => transaksiAssetKeluar::count(),
        ];

        return response()->json($counts);
    }
}
