<?php

namespace App\Http\Controllers;

use App\Models\refDivisi;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class refDivisiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $ref_divisi = refDivisi::all();

        if(count($ref_divisi) > 0){
            return response([
                'message' => 'Retrieve All Success',
                'data' => $ref_divisi
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
            'nama_divisi' => 'required|unique:ref_divisi',
        ]);

        if($validate->fails()) {
            return response()->json($validate->errors(), 400);
        }

        $ref_divisi = refDivisi::create([
            'nama_divisi' => $request->nama_divisi,
        ]);

        $storeData = refDivisi::latest()->first();
 
        return response([
            'message' => 'Add Divisi Success',
            'data' => $storeData
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show($id_divisi)
    {
        $ref_divisi = refDivisi::find($id_divisi);

        if(!is_null($ref_divisi)){
            return response([
                'message' => 'Retrieve Divisi Success!',
                'data' => $ref_divisi
            ], 200);
        };

        return response([
            'message' => 'Divisi Not Found',
            'data' => null
        ], 404);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id_divisi)
    {
        $ref_divisi = refDivisi::find($id_divisi);

        if(is_null($ref_divisi)){
            return response([
                'message' => 'Divisi Not Found!',
                'data' => null
            ], 404);
        };

        $updateData = $request->all();

        $validate  = Validator::make($updateData, [
            'nama_divisi' => 'required|unique:ref_divisi',
        ]);

        if($validate->fails()) {
            return response()->json($validate->errors(), 400);
        }

        $ref_divisi->nama_divisi = $updateData['nama_divisi'];

        if($ref_divisi->save()){
            return response([
                'message' => 'Update Divisi Success!',
                'data' => $ref_divisi
            ], 200);
        }

        return response([
            'message' => 'Update Divisi Failed!',
            'data' => null
        ], 400);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id_divisi)
    {
        $ref_divisi = refDivisi::find($id_divisi);

        if(is_null($ref_divisi)){
            return response([
                'message' => 'Divisi Not Found!',
                'data' => null
            ], 404);
        }

        if($ref_divisi->delete()){
            return response([
                'message' => 'Delete Divisi Success!',
                'data' => $ref_divisi
            ], 200);
        }

        return response([
            'message' => 'Delete Divisi Failed!',
            'data' => null
        ],400);
    }
}
