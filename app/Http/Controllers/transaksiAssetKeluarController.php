<?php

namespace App\Http\Controllers;

use App\Models\transaksiAssetKeluar;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class transaksiAssetKeluarController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $assetKeluar = transaksiAssetKeluar::all();

        if(count($assetKeluar) > 0){
            return response([
                'message' => 'Retrieve All Success',
                'data' => $assetKeluar
            ], 200);
        }

        return response([
            'message' => 'Empty',
            'data' => null
        ], 400);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $storeData = $request->all();
        $validate  = Validator::make($storeData, [
            'id_asset' => 'required|exists:asset,id_asset',
            'id' => 'required|exists:users,id',
            'tgl_keluar' => 'required',
            'keterangan' => 'required',
        ]);

        if($validate->fails()) {
            return response()->json($validate->errors(), 400);
        }

        $assetKeluar = transaksiAssetKeluar::create([
            'id_asset' => $request->id_asset,
            'id' => $request->id,
            'tgl_keluar' => $request->tgl_keluar,
            'keterangan' => $request->keterangan
        ]);

        $storeData = transaksiAssetKeluar::latest()->first();
 
        return response([
            'message' => 'Add Transaksi Keluar Success',
            'data' => $storeData
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show($id_asset)
    {
        $assetKeluar = transaksiAssetKeluar::find($id_asset);

        if(!is_null($assetKeluar)){
            return response([
                'message' => 'Retrieve Transaksi Keluar Success!',
                'data' => $assetKeluar
            ], 200);
        };

        return response([
            'message' => 'Transaksi Keluar Not Found',
            'data' => null
        ], 404);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id_asset)
    {
        $assetKeluar = transaksiAssetKeluar::find($id_asset);

        if(is_null($assetKeluar)){
            return response([
                'message' => 'Transaksi Keluar Not Found!',
                'data' => null
            ], 404);
        };

        $updateData = $request->all();

        $validate  = Validator::make($updateData, [
            'id_asset' => 'required|exists:asset,id_asset',
            'id' => 'required|exists:users,id',
            'tgl_keluar' => 'required',
            'keterangan' => 'required',
        ]);

        if($validate->fails()) {
            return response()->json($validate->errors(), 400);
        }

        $assetKeluar->id_asset = $updateData['id_asset'];
        $assetKeluar->id = $updateData['id'];
        $assetKeluar->tgl_keluar = $updateData['tgl_keluar'];
        $assetKeluar->keterangan = $updateData['keterangan'];

        if($assetKeluar->save()){
            return response([
                'message' => 'Update Transaksi Keluar Success!',
                'data' => $assetKeluar
            ], 200);
        }

        return response([
            'message' => 'Update Transaksi Keluar Failed!',
            'data' => null
        ], 400);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id_asset)
    {
        $assetKeluar = transaksiAssetKeluar::find($id_asset);

        if(is_null($assetKeluar)){
            return response([
                'message' => 'Transaksi Keluar Not Found!',
                'data' => null
            ], 404);
        }

        if($assetKeluar->delete()){
            return response([
                'message' => 'Delete Transaksi Keluar Success!',
                'data' => $assetKeluar
            ], 200);
        }

        return response([
            'message' => 'Delete Transaksi Keluar Failed!',
            'data' => null
        ],400);
    }
}
