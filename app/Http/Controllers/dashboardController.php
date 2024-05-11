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
    public function getRoleCount()
    {
        $count = refRole::count();
        return response()->json(['count' => $count]);
    }

    public function getUserCount()
    {
        $count = User::count();
        return response()->json(['count' => $count]);
    }

    public function getLokasiCount()
    {
        $count = refLokasi::count();
        return response()->json(['count' => $count]);
    }

    public function getKelasAsetCount()
    {
        $count = refKelasAset::count(); 
        return response()->json(['count' => $count]);
    }

    public function getKodeProjekCount()
    {
        $count = refKodeProjek::count();
        return response()->json(['count' => $count]);
    }

    public function getDivisiCount()
    {
        $count = refDivisi::count();
        return response()->json(['count' => $count]);
    }

    public function getAssetCount()
    {
        $count = asset::count();
        return response()->json(['count' => $count]);
    }

    public function getAssetMasukCount()
    {
        $count = transaksiAssetMasuk::count();
        return response()->json(['count' => $count]);
    }
    public function getAssetKeluarCount()
    {
        $count = transaksiAssetKeluar::count();
        return response()->json(['count' => $count]);
    }
}
