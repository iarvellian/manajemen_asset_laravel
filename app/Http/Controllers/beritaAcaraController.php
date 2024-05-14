<?php

namespace App\Http\Controllers;

use App\Models\beritaAcara;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class beritaAcaraController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $berita_acara = beritaAcara::with('assets')->get();

        if(count($berita_acara) > 0){
            return response([
                'message' => 'Retrieve All Success',
                'data' => $berita_acara
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
            'jenis' => 'required|string|in:masuk,keluar,rusak',
            'nomor_berita_acara' => 'required|string',
            'perihal' => 'required|string',
            'lokasi' => 'nullable|string',
            'tgl_cetak' => 'required|date',
            'pihak1' => 'required|string',
            'pihak2' => 'required|string',
            'jabatan1' => 'required|string',
            'jabatan2' => 'required|string',
            'keterangan' => 'nullable|string',
            'assets' => 'nullable|array',
            'assets.*' => 'exists:asset,id_asset',
            'gambar' => 'nullable|array',
        ]);

        if($validate->fails()) {
            return response()->json($validate->errors(), 400);
        }

        $berita_acara = beritaAcara::create([
            'jenis' => $request->jenis,
            'nomor_berita_acara' => $request->nomor_berita_acara,
            'perihal' => $request->perihal,
            'lokasi' => $request->lokasi,
            'tgl_cetak' => $request->tgl_cetak,
            'pihak1' => $request->pihak1,
            'pihak2' => $request->pihak2,
            'jabatan1' => $request->jabatan1,
            'jabatan2' => $request->jabatan2,
            'keterangan' => $request->keterangan,
            'gambar' => $request->gambar,
        ]);

        if(isset($storeData['assets'])) {
            $berita_acara->assets()->sync($storeData['assets']);
        }

        $storeData = beritaAcara::latest()->first();
 
        return response([
            'message' => 'Add Berita Acara Success',
            'data' => $storeData
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show($id_berita_acara)
    {
        $berita_acara = beritaAcara::with('assets')->find($id_berita_acara);

        if(!is_null($berita_acara)){
            return response([
                'message' => 'Retrieve Berita Acara Success!',
                'data' => $berita_acara
            ], 200);
        };

        return response([
            'message' => 'Berita Acara Not Found',
            'data' => null
        ], 404);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id_berita_acara)
    {
        $berita_acara = beritaAcara::find($id_berita_acara);

        if(is_null($berita_acara)){
            return response([
                'message' => 'Berita Acara Not Found!',
                'data' => null
            ], 404);
        };

        $updateData = $request->all();

        $validate  = Validator::make($updateData, [
            'jenis' => 'required|string|in:masuk,keluar,rusak',
            'nomor_berita_acara' => 'required|string',
            'perihal' => 'required|string',
            'lokasi' => 'nullable|string',
            'tgl_cetak' => 'required|date',
            'pihak1' => 'required|string',
            'pihak2' => 'required|string',
            'jabatan1' => 'required|string',
            'jabatan2' => 'required|string',
            'keterangan' => 'nullable|string',
            'assets' => 'nullable|array',
            'assets.*' => 'exists:asset,id_asset',
            'gambar' => 'nullable|array',
        ]);

        if($validate->fails()) {
            return response()->json($validate->errors(), 400);
        }

        $berita_acara->update($updateData);

        if(isset($updateData['assets'])) {
            $berita_acara->assets()->sync($updateData['assets']);
        }

        $updateData = beritaAcara::with('assets')->latest()->first();

        if($berita_acara->save()){
            return response([
                'message' => 'Update Berita Acara Success!',
                'data' => $berita_acara
            ], 200);
        }

        return response([
            'message' => 'Update Berita Acara Failed!',
            'data' => null
        ], 400);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id_berita_acara)
    {
        $berita_acara = beritaAcara::find($id_berita_acara);

        if(is_null($berita_acara)){
            return response([
                'message' => 'Berita Acara Not Found!',
                'data' => null
            ], 404);
        }

        if($berita_acara->delete()){
            return response([
                'message' => 'Delete Berita Acara Success!',
                'data' => $berita_acara
            ], 200);
        }

        return response([
            'message' => 'Delete Berita Acara Failed!',
            'data' => null
        ],400);
    }
}
