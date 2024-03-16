<?php

namespace App\Http\Controllers;

use App\Models\refKodeProjek;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class refKodeProjekController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $ref_kode_projek = refKodeProjek::all();

        if(count($ref_kode_projek) > 0){
            return response([
                'message' => 'Retrieve All Success',
                'data' => $ref_kode_projek
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
            'nama_kode_projek' => 'required',
        ]);

        if($validate->fails()) {
            return response()->json($validate->errors(), 400);
        }

        $ref_kode_projek = refKodeProjek::create([
            'nama_kode_projek' => $request->nama_kode_projek,
        ]);

        $storeData = refKodeProjek::latest()->first();
 
        return response([
            'message' => 'Add Kode Projek Success',
            'data' => $storeData
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show($id_kode_projek)
    {
        $ref_kode_projek = refKodeProjek::find($id_kode_projek);

        if(!is_null($ref_kode_projek)){
            return response([
                'message' => 'Retrieve Kode Projek Success!',
                'data' => $ref_kode_projek
            ], 200);
        };

        return response([
            'message' => 'Kode Projek Not Found',
            'data' => null
        ], 404);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id_kode_projek)
    {
        $ref_kode_projek = refKodeProjek::find($id_kode_projek);

        if(is_null($ref_kode_projek)){
            return response([
                'message' => 'Kode Projek Not Found!',
                'data' => null
            ], 404);
        };

        $updateData = $request->all();

        $validate  = Validator::make($updateData, [
            'nama_kode_projek' => 'required',
        ]);

        if($validate->fails()) {
            return response()->json($validate->errors(), 400);
        }

        $ref_kode_projek->nama_kode_projek = $updateData['nama_kode_projek'];

        if($ref_kode_projek->save()){
            return response([
                'message' => 'Update Kode Projek Success!',
                'data' => $ref_kode_projek
            ], 200);
        }

        return response([
            'message' => 'Update Kode Projek Failed!',
            'data' => null
        ], 400);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id_kode_projek)
    {
        $ref_kode_projek = refKodeProjek::find($id_kode_projek);

        if(is_null($ref_kode_projek)){
            return response([
                'message' => 'Kode Projek Not Found!',
                'data' => null
            ], 404);
        }

        if($ref_kode_projek->delete()){
            return response([
                'message' => 'Delete Kode Projek Success!',
                'data' => $ref_kode_projek
            ], 200);
        }

        return response([
            'message' => 'Delete Kode Projek Failed!',
            'data' => null
        ],400);
    }
}
