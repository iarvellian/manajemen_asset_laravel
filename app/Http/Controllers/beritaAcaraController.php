<?php

namespace App\Http\Controllers;

use App\Models\beritaAcara;
use App\Http\Controllers\Controller;
use App\Models\beritaAcaraGambar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use setasign\Fpdi\Fpdi;

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

        $berita_acara = beritaAcara::with('assets', 'images')->find($berita_acara->id_berita_acara);

        // Generate the PDF
        $pdfContent = $this->generatePdf($berita_acara);

        return response($pdfContent, 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="berita_acara.pdf"');
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

    public function generatePdf($berita_acara)
    {
        $pdf = new Fpdi();
        $pdf->AddPage();

        // Set font and add content based on jenis
        if ($berita_acara->jenis === 'rusak') {
            $this->pdfRusak($pdf, $berita_acara);
        } else {
            $this->pdfKeluarMasuk($pdf, $berita_acara);
        }

        return $pdf->Output('S');
    }

    private function pdfRusak($pdf, $berita_acara)
    {
        // Use the template
        $pdf->setSourceFile(storage_path('app/public/templates/KOP_Baru.pdf'));
        $templateId = $pdf->importPage(1);
        $pdf->useTemplate($templateId, 10, 10, 190);

        // Title
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->Cell(0, 10, 'BERITA ACARA ASSET RUSAK', 0, 1, 'C');

        // Nomor
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(0, 10, 'Nomor: ' . $berita_acara->nomor_berita_acara, 0, 1, 'C');

        // Perihal
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 10, 'Perihal: ' . $berita_acara->perihal, 0, 1, 'C');
        $pdf->Ln(10);

        // Keterangan
        $pdf->SetFont('Arial', '', 12);
        $pdf->MultiCell(0, 10, $berita_acara->keterangan);
        $pdf->Ln(10);

        // Table header
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(90, 10, 'Nama Aset', 1);
        $pdf->Cell(90, 10, 'Jumlah', 1);
        $pdf->Ln();

        // Table content
        $pdf->SetFont('Arial', '', 12);
        foreach ($berita_acara->assets as $asset) {
            $pdf->Cell(90, 10, $asset->nama_aset, 1);
            $pdf->Cell(90, 10, $asset->jumlah_fisik, 1);
            $pdf->Ln();
        }
        $pdf->Ln(10);

        // Final text
        $pdf->MultiCell(0, 10, 'Demikian Berita Acara ini dibuat dengan sebenarnya untuk dapat dipergunakan sebagaimana mestinya.');
        $pdf->Ln(20);

        // Signatures
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(60, 10, 'Pihak Pertama:', 0, 0, 'L');
        $pdf->Cell(60, 10, 'Pihak Kedua:', 0, 0, 'R');
        $pdf->Cell(0, 10, '', 0, 1); // Empty cell for spacing
        $pdf->Ln(20);
        $pdf->Cell(60, 10, $berita_acara->pihak_pertama, 0, 0, 'L');
        $pdf->Cell(60, 10, $berita_acara->pihak_kedua, 0, 0, 'R');
        $pdf->Ln(5);
        $pdf->Cell(60, 10, $berita_acara->jabatan_pertama, 0, 0, 'L');
        $pdf->Cell(60, 10, $berita_acara->jabatan_kedua, 0, 0, 'R');
        $pdf->Ln(20);
        $pdf->Cell(0, 10, 'Pihak Ketiga:', 0, 1, 'C');
        $pdf->Ln(20);
        $pdf->Cell(0, 10, '$berita_acara->pihak_ketiga', 0, 1, 'C');
        $pdf->Ln(5);
        $pdf->Cell(0, 10, '$berita_acara->jabatan_ketiga', 0, 1, 'C');

        // New page for lampiran
        $pdf->AddPage();

        // Title for lampiran
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->Cell(0, 10, 'LAMPIRAN', 0, 1, 'C');

        // Nomor
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(0, 10, 'Nomor: ' . $berita_acara->nomor_berita_acara, 0, 1, 'C');

        // Description
        $pdf->Cell(0, 10, '(Foto barang yang rusak)', 0, 1, 'C');

        // Photos
        if ($berita_acara->images->isNotEmpty()) {
            $pdf->Ln(10);
            $pdf->SetFont('Arial', 'B', 14);
            $pdf->Cell(40, 10, 'Images:', 0, 1);
            $pdf->SetFont('Arial', '', 12);

            foreach ($berita_acara->images as $image) {
                $pdf->Image(storage_path('app/' . $image->path), null, null, 150);
                $pdf->Ln(10);
            }
        }
    }

    private function pdfKeluarMasuk($pdf, $berita_acara)
    {
        // Title
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->Cell(0, 10, 'BERITA ACARA KELUAR / MASUK BARANG GUDANG', 0, 1, 'C');
        $pdf->Ln(5);

        // Details section
        $pdf->SetFont('Arial', '', 12);
        $details = [
            'Nomor' => $berita_acara->nomor_berita_acara,
            'Perihal' => $berita_acara->perihal,
            'Lokasi' => $berita_acara->lokasi,
            'Hari/Tanggal' => $berita_acara->tgl_cetak,
        ];

        foreach ($details as $label => $value) {
            $pdf->Cell(40, 10, $label . ' : ', 0, 0);
            $pdf->Cell(0, 10, $value, 0, 1);
        }
        $pdf->Ln(5);

        // Divider
        $pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY());
        $pdf->Ln(5);

        // Pihak Pertama
        $pdf->Cell(0, 10, 'Yang bertanda tangan di Bawah ini:', 0, 1);
        $pdf->Cell(40, 10, 'Nama : ', 0, 0);
        $pdf->Cell(0, 10, $berita_acara->pihak_pertama, 0, 1);
        $pdf->Cell(40, 10, 'Jabatan : ', 0, 0);
        $pdf->Cell(0, 10, $berita_acara->jabatan_pertama, 0, 1);
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 10, 'Dalam hal ini disebut sebagai pihak Pertama', 0, 1);
        $pdf->Ln(5);

        // Pihak Kedua
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(40, 10, 'Nama : ', 0, 0);
        $pdf->Cell(0, 10, $berita_acara->pihak_kedua, 0, 1);
        $pdf->Cell(40, 10, 'Jabatan : ', 0, 0);
        $pdf->Cell(0, 10, $berita_acara->jabatan_kedua, 0, 1);
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 10, 'Dalam hal ini disebut sebagai pihak Kedua', 0, 1);
        $pdf->Ln(5);

        // Menerangkan text
        $pdf->SetFont('Arial', '', 12);
        $pdf->MultiCell(0, 10, 'Dengan ini menerangkan bahwa Pihak Pertama telah melakukan pengambilan barang Operasional ICT berupa:');
        $pdf->Ln(5);

        // Table header
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(60, 10, 'Nama Aset', 1);
        $pdf->Cell(60, 10, 'Serial Number', 1);
        $pdf->Cell(60, 10, 'Kondisi', 1);
        $pdf->Ln();

        // Table content
        $pdf->SetFont('Arial', '', 12);
        foreach ($berita_acara->assets as $asset) {
            $pdf->Cell(60, 10, $asset->nama_aset, 1);
            $pdf->Cell(60, 10, $asset->serial_number, 1);
            $pdf->Cell(60, 10, $asset->kondisi, 1);
            $pdf->Ln();
        }
        $pdf->Ln(10);

        // Final text
        $pdf->MultiCell(0, 10, 'Demikian Berita Acara ini dibuat dengan sebenarnya untuk dapat dipergunakan sebagaimana mestinya.');
        $pdf->Ln(20);

        // Signatures
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(90, 10, 'Pihak Pertama:', 0, 0, 'L');
        $pdf->Cell(0, 10, 'Pihak Kedua:', 0, 1, 'R');
        $pdf->Ln(20);
        $pdf->Cell(90, 10, $berita_acara->pihak_pertama, 0, 0, 'L');
        $pdf->Cell(0, 10, $berita_acara->pihak_kedua, 0, 1, 'R');
        $pdf->Ln(5);
        $pdf->Cell(90, 10, $berita_acara->jabatan_pertama, 0, 0, 'L');
        $pdf->Cell(0, 10, $berita_acara->jabatan_kedua, 0, 1, 'R');
    }
}
