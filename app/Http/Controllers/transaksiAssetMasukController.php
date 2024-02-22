<?php

namespace App\Http\Controllers;

use App\Models\transaksiAssetMasuk;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class transaksiAssetMasukController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $assetMasuk = transaksiAssetMasuk::all();

        if(count($assetMasuk) > 0){
            return response([
                'message' => 'Retrieve All Success',
                'data' => $assetMasuk
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
            'tgl_masuk' => 'required',
            'keterangan' => 'required',
        ]);

        if($validate->fails()) {
            return response()->json($validate->errors(), 400);
        }

        $assetMasuk = transaksiAssetMasuk::create([
            'id_asset' => $request->id_asset,
            'id' => $request->id,
            'tgl_masuk' => $request->tgl_masuk,
            'keterangan' => $request->keterangan
        ]);

        $storeData = transaksiAssetMasuk::latest()->first();
 
        return response([
            'message' => 'Add Transaksi Masuk Success',
            'data' => $storeData
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show($id_asset)
    {
        $assetMasuk = transaksiAssetMasuk::find($id_asset);

        if(!is_null($assetMasuk)){
            return response([
                'message' => 'Retrieve Transaksi Masuk Success!',
                'data' => $assetMasuk
            ], 200);
        };

        return response([
            'message' => 'Transaksi Masuk Not Found',
            'data' => null
        ], 404);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id_asset)
    {
        $assetMasuk = transaksiAssetMasuk::find($id_asset);

        if(is_null($assetMasuk)){
            return response([
                'message' => 'Transaksi Masuk Not Found!',
                'data' => null
            ], 404);
        };

        $updateData = $request->all();

        $validate  = Validator::make($updateData, [
            'id_asset' => 'required|exists:asset,id_asset',
            'id' => 'required|exists:users,id',
            'tgl_masuk' => 'required',
            'keterangan' => 'required',
        ]);

        if($validate->fails()) {
            return response()->json($validate->errors(), 400);
        }

        $assetMasuk->id_asset = $updateData['id_asset'];
        $assetMasuk->id = $updateData['id'];
        $assetMasuk->tgl_masuk = $updateData['tgl_masuk'];
        $assetMasuk->keterangan = $updateData['keterangan'];

        if($assetMasuk->save()){
            return response([
                'message' => 'Update Transaksi Masuk Success!',
                'data' => $assetMasuk
            ], 200);
        }

        return response([
            'message' => 'Update Transaksi Masuk Failed!',
            'data' => null
        ], 400);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id_asset)
    {
        $assetMasuk = transaksiAssetMasuk::find($id_asset);

        if(is_null($assetMasuk)){
            return response([
                'message' => 'Transaksi Masuk Not Found!',
                'data' => null
            ], 404);
        }

        if($assetMasuk->delete()){
            return response([
                'message' => 'Delete Transaksi Masuk Success!',
                'data' => $assetMasuk
            ], 200);
        }

        return response([
            'message' => 'Delete Transaksi Masuk Failed!',
            'data' => null
        ],400);
    }
}
