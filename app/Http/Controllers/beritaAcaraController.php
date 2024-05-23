<?php

namespace App\Http\Controllers;

use App\Models\beritaAcara;
use App\Http\Controllers\Controller;
use App\Models\beritaAcaraGambar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use FPDF;

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
        $rules = [
            'jenis' => 'required|string|in:masuk,keluar,rusak',
            'nomor_berita_acara' => 'required|string',
            'perihal' => 'required|string',
            'lokasi' => 'required|string',
            'tgl_cetak' => '',
            'pihak_pertama' => 'required|string',
            'pihak_kedua' => 'required|string',
            'jabatan_pertama' => 'required|string',
            'jabatan_kedua' => 'required|string',
            'keterangan' => 'required|string',
            'assets' => 'required|array',
            'assets.*' => 'exists:asset,id_asset',
            'gambar' => 'nullable|array',
            'gambar.*' => 'mimes:jpeg,jpg,png,bmp,gif,svg,webp|max:2048',
        ];
    
        if ($request->jenis === 'rusak') {
            $rules['gambar'] = 'required|array';
        }
    
        $validate = Validator::make($storeData, $rules);

        if($validate->fails()) {
            return response()->json($validate->errors(), 400);
        }

        $berita_acara = beritaAcara::create([
            'jenis' => $request->jenis,
            'nomor_berita_acara' => $request->nomor_berita_acara,
            'perihal' => $request->perihal,
            'lokasi' => $request->lokasi,
            'tgl_cetak' => now(),
            'pihak_pertama' => $request->pihak_pertama,
            'pihak_kedua' => $request->pihak_kedua,
            'jabatan_pertama' => $request->jabatan_pertama,
            'jabatan_kedua' => $request->jabatan_kedua,
            'keterangan' => $request->keterangan,
        ]);

        if (isset($storeData['assets'])) {
            $berita_acara->assets()->sync($storeData['assets']);
        }

        if ($request->hasFile('gambar')) {
            $images = $request->file('gambar');
            foreach ($images as $image) {
                $path = $image->store('public/images');
                beritaAcaraGambar::create([
                    'id_berita_acara' => $berita_acara->id_berita_acara,
                    'path' => $path,
                ]);
            }
        }

        $storeData = beritaAcara::with('assets', 'images')->latest()->first();

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
        ], 400);
    }

    public function generatePdf($id_berita_acara)
    {
        $berita_acara = beritaAcara::with('assets')->find($id_berita_acara);

        if (is_null($berita_acara)) {
            return response([
                'message' => 'Berita Acara Not Found',
                'data' => null
            ], 404);
        }

        // Create instance of FPDF
        $pdf = new FPDF();
        $pdf->AddPage();

        // Set font
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->Cell(0, 10, 'Berita Acara Details', 0, 1, 'C');

        // Add data to the PDF
        $pdf->SetFont('Arial', '', 12);
        $pdf->Ln(10);
        $pdf->Cell(40, 10, 'Nomor Berita Acara: ' . $berita_acara->nomor_berita_acara);
        $pdf->Ln(10);
        $pdf->Cell(40, 10, 'Perihal: ' . $berita_acara->perihal);
        $pdf->Ln(10);
        $pdf->Cell(40, 10, 'Lokasi: ' . $berita_acara->lokasi);
        $pdf->Ln(10);
        $pdf->Cell(40, 10, 'Tanggal Cetak: ' . $berita_acara->tgl_cetak);
        $pdf->Ln(10);
        $pdf->Cell(40, 10, 'Pihak 1: ' . $berita_acara->pihak1 . ' (' . $berita_acara->jabatan1 . ')');
        $pdf->Ln(10);
        $pdf->Cell(40, 10, 'Pihak 2: ' . $berita_acara->pihak2 . ' (' . $berita_acara->jabatan2 . ')');
        $pdf->Ln(10);
        $pdf->Cell(40, 10, 'Keterangan: ' . $berita_acara->keterangan);
        $pdf->Ln(10);

        if ($berita_acara->assets->isNotEmpty()) {
            $pdf->Ln(10);
            $pdf->SetFont('Arial', 'B', 14);
            $pdf->Cell(40, 10, 'Assets:', 0, 1);
            $pdf->SetFont('Arial', '', 12);

            foreach ($berita_acara->assets as $asset) {
                $pdf->Cell(40, 10, '- ' . $asset->nama_aset);
                $pdf->Ln(10);
            }
        }

        // Output the PDF
        return response($pdf->Output('S'), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="berita_acara.pdf"');
    }
}
