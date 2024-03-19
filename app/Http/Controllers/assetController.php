<?php

namespace App\Http\Controllers;

use App\Models\asset;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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
        $storeData = $request->all();
        $validate  = Validator::make($storeData, [
            'id_divisi' => 'required|exists:ref_divisi,id_divisi',
            'id_lokasi' => 'required|exists:ref_lokasi,id_lokasi',
            'id_kelas_aset' => 'required|exists:ref_kelas_aset,id_kelas_aset',
            'id_kode_projek' => 'required|exists:ref_kode_projek,id_kode_projek',
            'is_luar_kota' => 'required',
            'thn_perolehan' => 'required|digits:4|integer|min:1900|max:9999',
            'cost_center' => 'required',
            'ue' => 'required',
            'kode_aset' => 'required',
            'nama_aset' => 'required',
            'jumlah_sap' => 'required',
            'jumlah_fisik' => 'required',
            'kondisi' => 'required',
            'pic_aset' => 'required',
            'pic_project' => 'required',
            'serial_number' => 'required',
            'no_rangka_kendaraan' => 'required',
            'no_mesin_kendaraan' => 'required',
            'no_plat_kendaraan' => 'required',
        ]);

        if($validate->fails()) {
            return response()->json($validate->errors(), 400);
        }

        $assets = asset::create([
            'id_divisi' => $request->id_divisi,
            'id_lokasi' => $request->id_lokasi,
            'id_kelas_aset' => $request->id_kelas_aset,
            'id_kode_projek' => $request->id_kode_projek,
            'is_luar_kota' => $request->is_luar_kota,
            'thn_perolehan' => $request->thn_perolehan,
            'cost_center' => $request->cost_center,
            'ue' => $request->ue,
            'kode_aset' => $request->kode_aset,
            'nama_aset' => $request->nama_aset,
            'jumlah_sap' => $request->jumlah_sap,
            'jumlah_fisik' => $request->jumlah_fisik,
            'kondisi' => $request->kondisi,
            'pic_aset' => $request->pic_aset,
            'pic_project' => $request->pic_project,
            'serial_number' => $request->serial_number,
            'no_rangka_kendaraan' => $request->no_rangka_kendaraan,
            'no_mesin_kendaraan' => $request->no_mesin_kendaraan,
            'no_plat_kendaraan' => $request->no_plat_kendaraan,
        ]);

        $storeData = asset::latest()->first();
 
        return response([
            'message' => 'Add Asset Success',
            'data' => $storeData
        ], 200);
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
            'cost_center' => 'required',
            'ue' => 'required',
            'kode_aset' => 'required',
            'nama_aset' => 'required',
            'jumlah_sap' => 'required',
            'jumlah_fisik' => 'required',
            'kondisi' => 'required',
            'pic_aset' => 'required',
            'pic_project' => 'required',
            'serial_number' => 'required',
            'no_rangka_kendaraan' => 'required',
            'no_mesin_kendaraan' => 'required',
            'no_plat_kendaraan' => 'required',
        ]);

        if($validate->fails()) {
            return response()->json($validate->errors(), 400);
        }

        $assets->id_divisi = $updateData['id_divisi'];
        $assets->id_lokasi = $updateData['id_lokasi'];
        $assets->id_kelas_aset = $updateData['id_kelas_aset'];
        $assets->id_kode_projek = $updateData['id_kode_projek'];
        $assets->is_luar_kota = $updateData['is_luar_kota'];
        $assets->thn_perolehan = $updateData['thn_perolehan'];
        $assets->cost_center = $updateData['cost_center'];
        $assets->ue = $updateData['ue'];
        $assets->kode_aset = $updateData['kode_aset'];
        $assets->nama_aset = $updateData['nama_aset'];
        $assets->jumlah_sap = $updateData['jumlah_sap'];
        $assets->jumlah_fisik = $updateData['jumlah_fisik'];
        $assets->kondisi = $updateData['kondisi'];
        $assets->pic_aset = $updateData['pic_aset'];
        $assets->pic_project = $updateData['pic_project'];
        $assets->serial_number = $updateData['serial_number'];
        $assets->no_rangka_kendaraan = $updateData['no_rangka_kendaraan'];
        $assets->no_mesin_kendaraan = $updateData['no_mesin_kendaraan'];
        $assets->no_plat_kendaraan = $updateData['no_plat_kendaraan'];

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
        ],400);
    }
}
