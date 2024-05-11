<?php

namespace App\Http\Controllers;

use App\Models\transaksiAssetKeluar;
use App\Models\transaksiAssetMasuk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class transaksiAsset extends Controller
{
    public function showAssetMasuk()
    {
        $assetMasuk = transaksiAssetMasuk::with(['asset', 'user'])->get();

        if (count($assetMasuk) > 0) {
            return response([
                'message' => 'Retrieve All Success',
                'data' => $assetMasuk
            ], 200);
        }

        return response([
            'message' => 'Empty',
            'data' => []
        ], 200);
    }

    public function showAssetKeluar()
    {
        $assetKeluar = transaksiAssetKeluar::with(['asset', 'user'])->get();

        if (count($assetKeluar) > 0) {
            return response([
                'message' => 'Retrieve All Success',
                'data' => $assetKeluar
            ], 200);
        }

        return response([
            'message' => 'Empty',
            'data' => []
        ], 200);
    }

    public function storeTransaksiAssetMasuk(Request $request)
    {
        $validate  = Validator::make($request->all(), [
            'selected_assets' => 'required|array',
            'selected_assets.*.id_asset' => 'required|exists:asset,id_asset',
            'selected_assets.*.id' => 'required|exists:users,id',
            'tgl_masuk' => '',
            'keterangan' => '',
        ]);

        if ($validate->fails()) {
            return response()->json($validate->errors(), 400);
        }

        $selectedAssets = $request->input('selected_assets');

        $assetsMasuk = [];
        foreach ($selectedAssets as $selectedAsset) {
            $assetMasuk = transaksiAssetMasuk::create([
                'id_asset' => $selectedAsset['id_asset'],
                'id' => $selectedAsset['id'],
                'tgl_masuk' => now(),
                'keterangan' => "Asset Dikembalikan"
            ]);
            $assetsMasuk[] = $assetMasuk;

            transaksiAssetKeluar::where('id_asset', $selectedAsset['id_asset'])->delete();
        }

        return response([
            'message' => 'Assets successfully moved from transaksi_asset_keluar to transaksi_asset_masuk',
            'data' => $assetsMasuk
        ], 200);
    }

    public function storeTransaksiAssetKeluar(Request $request)
    {
        $validate  = Validator::make($request->all(), [
            'selected_assets' => 'required|array',
            'selected_assets.*.id_asset' => 'required|exists:asset,id_asset',
            'selected_assets.*.id' => 'required|exists:users,id',
            'tgl_keluar' => '',
            'keterangan' => '',
        ]);

        if ($validate->fails()) {
            return response()->json($validate->errors(), 400);
        }

        $selectedAssets = $request->input('selected_assets');

        $assetsKeluar = [];
        foreach ($selectedAssets as $selectedAsset) {
            $assetKeluar = transaksiAssetKeluar::create([
                'id_asset' => $selectedAsset['id_asset'],
                'id' => $selectedAsset['id'],
                'tgl_keluar' => now(),
                'keterangan' => "Asset Dipinjam"
            ]);
            $assetsKeluar[] = $assetKeluar;

            transaksiAssetMasuk::where('id_asset', $selectedAsset['id_asset'])->delete();
        }

        return response([
            'message' => 'Assets successfully moved from transaksi_asset_masuk to transaksi_asset_keluar',
            'data' => $assetsKeluar
        ], 200);
    }
}
