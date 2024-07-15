<?php

namespace App\Http\Controllers;

use App\Models\asset;
use App\Http\Controllers\Controller;
use App\Models\transaksiAssetMasuk;
use App\Imports\assetImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class assetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $assets = asset::with('divisi', 'kodeProjek', 'kelasAset', 'lokasi')->get();

        if(count($assets) > 0){
            return response([
                'message' => 'Retrieve All Success',
                'data' => $assets
            ], 200);
        }

        return response([
            'message' => 'Empty',
            'data' => []
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $customAttributes = [
            'id_divisi' => 'divisi',
            'id_lokasi' => 'lokasi',
            'id_kelas_aset' => 'kelas aset',
            'id_kode_projek' => 'kode projek',
            'thn_perolehan' => 'tahun perolehan',
        ];

        $storeData = $request->all();
        $validate  = Validator::make($storeData, [
            'id_divisi' => 'required|exists:ref_divisi,id_divisi',
            'id_lokasi' => 'required|exists:ref_lokasi,id_lokasi',
            'id_kelas_aset' => 'required|exists:ref_kelas_aset,id_kelas_aset',
            'id_kode_projek' => 'required|exists:ref_kode_projek,id_kode_projek',
            'is_luar_kota' => 'required',
            'thn_perolehan' => 'required|digits:4|integer|min:1901|max:2155',
            'cost_center' => '',
            'ue' => 'required',
            'kode_aset' => 'required',
            'nama_aset' => 'required',
            'jumlah_sap' => 'required',
            'jumlah_fisik' => 'required',
            'kondisi' => 'required',
            'pic_aset' => '',
            'pic_project' => '',
            'serial_number' => 'required|unique:asset',
            'no_rangka_kendaraan' => 'required',
            'no_mesin_kendaraan' => 'required',
            'no_plat_kendaraan' => 'required',
        ], [], $customAttributes);

        if ($validate->fails()) {
            return response()->json($validate->errors(), 400);
        }

        DB::beginTransaction();

        try {
            $user = Auth::user();

            $assets = asset::create([
                'id_divisi' => $request->id_divisi,
                'id_lokasi' => $request->id_lokasi,
                'id_kelas_aset' => $request->id_kelas_aset,
                'id_kode_projek' => $request->id_kode_projek,
                'is_luar_kota' => $request->is_luar_kota,
                'thn_perolehan' => $request->thn_perolehan,
                'cost_center' => "BPN",
                'ue' => $request->ue,
                'kode_aset' => $request->kode_aset,
                'nama_aset' => $request->nama_aset,
                'jumlah_sap' => $request->jumlah_sap,
                'jumlah_fisik' => $request->jumlah_fisik,
                'kondisi' => $request->kondisi,
                'pic_aset' => "M Syahrul",
                'pic_project' => $user->nama_pegawai,
                'serial_number' => $request->serial_number,
                'no_rangka_kendaraan' => $request->no_rangka_kendaraan,
                'no_mesin_kendaraan' => $request->no_mesin_kendaraan,
                'no_plat_kendaraan' => $request->no_plat_kendaraan,
            ]);

            $assetMasuk = transaksiAssetMasuk::create([
                'id_asset' => $assets->id_asset,
                'id_user' => $user->id,
                'tgl_masuk' => now(),
                'keterangan' => 'Aset Baru Masuk',
            ]);

            DB::commit();

            return response([
                'message' => 'Add Asset Success',
                'data' => $assets,
                'transaction_data' => $assetMasuk,
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response([
                'message' => 'Add Asset Failed!',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id_asset)
    {
        $assets = asset::with('divisi', 'kodeProjek', 'kelasAset', 'lokasi')->find($id_asset);

        if(!is_null($assets)){
            return response([
                'message' => 'Retrieve Asset Success!',
                'data' => $assets
            ], 200);
        };

        return response([
            'message' => 'Asset Not Found',
            'data' => null
        ], 404);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id_asset)
    {
        $assets = asset::find($id_asset);

        if(is_null($assets)){
            return response([
                'message' => 'Asset Not Found!',
                'data' => null
            ], 404);
        };

        $updateData = $request->all();

        $validate  = Validator::make($updateData, [
            'id_divisi' => 'required|exists:ref_divisi,id_divisi',
            'id_lokasi' => 'required|exists:ref_lokasi,id_lokasi',
            'id_kelas_aset' => 'required|exists:ref_kelas_aset,id_kelas_aset',
            'id_kode_projek' => 'required|exists:ref_kode_projek,id_kode_projek',
            'is_luar_kota' => 'required',
            'thn_perolehan' => 'required',
            'cost_center' => '',
            'ue' => 'required',
            'kode_aset' => 'required',
            'nama_aset' => 'required',
            'jumlah_sap' => 'required',
            'jumlah_fisik' => 'required',
            'kondisi' => 'required',
            'pic_aset' => '',
            'serial_number' => 'required|unique:asset',
            'no_rangka_kendaraan' => 'required',
            'no_mesin_kendaraan' => 'required',
            'no_plat_kendaraan' => 'required',
        ]);

        if ($validate->fails()) {
            return response()->json($validate->errors(), 400);
        }

        unset($updateData['cost_center']);
        unset($updateData['pic_aset']);

        $user = Auth::user();
        $updateData['pic_project'] = $user->nama_pegawai;

        $assets->fill($updateData);

        if($assets->save()){
            return response([
                'message' => 'Update Asset Success!',
                'data' => $assets
            ], 200);
        }

        return response([
            'message' => 'Update Asset Failed!',
            'data' => null
        ], 400);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id_asset)
    {
        $assets = asset::find($id_asset);

        if(is_null($assets)){
            return response([
                'message' => 'Asset Not Found!',
                'data' => null
            ], 404);
        }

        if($assets->delete()){
            return response([
                'message' => 'Delete Asset Success!',
                'data' => $assets
            ], 200);
        }

        return response([
            'message' => 'Delete Asset Failed!',
            'data' => null
        ], 400);
    }

    public function import(Request $request) 
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
        ]);

        Excel::import(new assetImport, $request->file('file'));

        return response([
            'message' => 'Import  Asset Success!',
        ], 200);
    }
}
