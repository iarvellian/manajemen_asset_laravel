<?php

namespace App\Http\Controllers;

use App\Models\refKelasAset;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class refKelasAsetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $ref_kelas_aset = refKelasAset::all();

        if(count($ref_kelas_aset) > 0){
            return response([
                'message' => 'Retrieve All Success',
                'data' => $ref_kelas_aset
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
            'nama_kelas_aset' => 'required|unique:ref_kelas_aset',
        ]);

        if($validate->fails()) {
            return response()->json($validate->errors(), 400);
        }

        $ref_kelas_aset = refKelasAset::create([
            'nama_kelas_aset' => $request->nama_kelas_aset,
        ]);

        $storeData = refKelasAset::latest()->first();
 
        return response([
            'message' => 'Add Kelas Aset Success',
            'data' => $storeData
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show($id_kelas_aset)
    {
        $ref_kelas_aset = refKelasAset::find($id_kelas_aset);

        if(!is_null($ref_kelas_aset)){
            return response([
                'message' => 'Retrieve Kelas Aset Success!',
                'data' => $ref_kelas_aset
            ], 200);
        };

        return response([
            'message' => 'Kelas Aset Not Found',
            'data' => null
        ], 404);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id_kelas_aset)
    {
        $ref_kelas_aset = refKelasAset::find($id_kelas_aset);

        if(is_null($ref_kelas_aset)){
            return response([
                'message' => 'Kelas Aset Not Found!',
                'data' => null
            ], 404);
        };

        $updateData = $request->all();

        $validate  = Validator::make($updateData, [
            'nama_kelas_aset' => 'required|unique:ref_kelas_aset',
        ]);

        if($validate->fails()) {
            return response()->json($validate->errors(), 400);
        }

        $ref_kelas_aset->nama_kelas_aset = $updateData['nama_kelas_aset'];

        if($ref_kelas_aset->save()){
            return response([
                'message' => 'Update Kelas Aset Success!',
                'data' => $ref_kelas_aset
            ], 200);
        }

        return response([
            'message' => 'Update Kelas Aset Failed!',
            'data' => null
        ], 400);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id_kelas_aset)
    {
        $ref_kelas_aset = refKelasAset::find($id_kelas_aset);

        if(is_null($ref_kelas_aset)){
            return response([
                'message' => 'Kelas Aset Not Found!',
                'data' => null
            ], 404);
        }

        if($ref_kelas_aset->delete()){
            return response([
                'message' => 'Delete Kelas Aset Success!',
                'data' => $ref_kelas_aset
            ], 200);
        }

        return response([
            'message' => 'Delete Kelas Aset Failed!',
            'data' => null
        ],400);
    }
}
