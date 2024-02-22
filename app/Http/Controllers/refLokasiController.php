<?php

namespace App\Http\Controllers;

use App\Models\refLokasi;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class refLokasiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $ref_lokasi = refLokasi::all();

        if(count($ref_lokasi) > 0){
            return response([
                'message' => 'Retrieve All Success',
                'data' => $ref_lokasi
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
            'nama_lokasi' => 'required',
        ]);

        if($validate->fails()) {
            return response()->json($validate->errors(), 400);
        }

        $ref_lokasi = refLokasi::create([
            'nama_lokasi' => $request->nama_lokasi,
        ]);

        $storeData = refLokasi::latest()->first();
 
        return response([
            'message' => 'Add Lokasi Success',
            'data' => $storeData
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show($id_lokasi)
    {
        $ref_lokasi = refLokasi::find($id_lokasi);

        if(!is_null($ref_lokasi)){
            return response([
                'message' => 'Retrieve Lokasi Success!',
                'data' => $ref_lokasi
            ], 200);
        };

        return response([
            'message' => 'Lokasi Not Found',
            'data' => null
        ], 404);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id_lokasi)
    {
        $ref_lokasi = refLokasi::find($id_lokasi);

        if(is_null($ref_lokasi)){
            return response([
                'message' => 'Lokasi Not Found!',
                'data' => null
            ], 404);
        };

        $updateData = $request->all();

        $validate  = Validator::make($updateData, [
            'nama_lokasi' => 'required',
        ]);

        if($validate->fails()) {
            return response()->json($validate->errors(), 400);
        }

        $ref_lokasi->nama_lokasi = $updateData['nama_lokasi'];

        if($ref_lokasi->save()){
            return response([
                'message' => 'Update Lokasi Success!',
                'data' => $ref_lokasi
            ], 200);
        }

        return response([
            'message' => 'Update Lokasi Failed!',
            'data' => null
        ], 400);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id_lokasi)
    {
        $ref_lokasi = refLokasi::find($id_lokasi);

        if(is_null($ref_lokasi)){
            return response([
                'message' => 'Lokasi Not Found!',
                'data' => null
            ], 404);
        }

        if($ref_lokasi->delete()){
            return response([
                'message' => 'Delete Lokasi Success!',
                'data' => $ref_lokasi
            ], 200);
        }

        return response([
            'message' => 'Delete Lokasi Failed!',
            'data' => null
        ],400);
    }
}
