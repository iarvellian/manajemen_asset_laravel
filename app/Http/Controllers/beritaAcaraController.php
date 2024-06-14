<?php

namespace App\Http\Controllers;

use App\Models\beritaAcara;
use App\Http\Controllers\Controller;
use App\Models\asset;
use App\Models\beritaAcaraGambar;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
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
            'nomor_berita_acara' => '',
            'perihal' => '',
            'lokasi' => '',
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
            $rules['perihal'] = 'required|string';
            $rules['gambar'] = 'required|array';
        }

        $validate = Validator::make($storeData, $rules);

        if($validate->fails()) {
            return response()->json($validate->errors(), 400);
        }

        // Format nomor_berita_acara and perihal based on jenis
        $year = date('Y');
        $romanMonth = [
            1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI',
            7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII'
        ];
        $month = $romanMonth[date('n')];

        if ($request->jenis === 'masuk' || $request->jenis === 'keluar') {
            $storeData['nomor_berita_acara'] = "BA/       /$month/$year/BM-BPN";
            $storeData['perihal'] = $request->jenis === 'masuk' ? 'Barang Masuk Gudang' : 'Barang Keluar Gudang';
            $storeData['lokasi'] = 'Gudang ICT';
        } elseif ($request->jenis === 'rusak') {
            $storeData['nomor_berita_acara'] = "APS.     /BA.    .    /$year/BM.BPN-B";
            $storeData['lokasi'] = 'Balikpapan';
        }

        date_default_timezone_set('Asia/Makassar');

        // Format tgl_cetak using DateTime for consistent results
        $date = new DateTime();
        $tgl_cetak_formatted = $date->format('j') . ' ' . strftime('%B %Y', $date->getTimestamp());

        if ($request->jenis === 'masuk' || $request->jenis === 'keluar') {
            $tgl_cetak_formatted = strftime('%A, ', $date->getTimestamp()) . $tgl_cetak_formatted;
        }

        // Manual translation fallback
        $hari_indonesia = [
            'Sunday' => 'Minggu',
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu',
        ];

        $bulan_indonesia = [
            'January' => 'Januari',
            'February' => 'Februari',
            'March' => 'Maret',
            'April' => 'April',
            'May' => 'Mei',
            'June' => 'Juni',
            'July' => 'Juli',
            'August' => 'Agustus',
            'September' => 'September',
            'October' => 'Oktober',
            'November' => 'November',
            'December' => 'Desember',
        ];

        // Apply manual translation if needed
        $tgl_cetak_formatted = str_replace(array_keys($hari_indonesia), array_values($hari_indonesia), $tgl_cetak_formatted);
        $tgl_cetak_formatted = str_replace(array_keys($bulan_indonesia), array_values($bulan_indonesia), $tgl_cetak_formatted);

        $tgl_cetak_mysql = date('Y-m-d'); // MySQL format

        // Create berita acara
        $berita_acara = beritaAcara::create([
            'jenis' => $storeData['jenis'],
            'nomor_berita_acara' => $storeData['nomor_berita_acara'],
            'perihal' => $storeData['perihal'],
            'lokasi' => $storeData['lokasi'],
            'tgl_cetak' => $tgl_cetak_mysql,
            'pihak_pertama' => $storeData['pihak_pertama'],
            'pihak_kedua' => $storeData['pihak_kedua'],
            'jabatan_pertama' => $storeData['jabatan_pertama'],
            'jabatan_kedua' => $storeData['jabatan_kedua'],
            'keterangan' => $storeData['keterangan'],
        ]);

        if (isset($storeData['assets'])) {
            $berita_acara->assets()->sync($storeData['assets']);
    
            // If jenis is 'rusak', update the asset's kondisi to 'rusak'
            if ($request->jenis === 'rusak') {
                foreach ($storeData['assets'] as $assetId) {
                    $asset = asset::find($assetId);
                    if ($asset) {
                        $asset->kondisi = 'Rusak';
                        $asset->save();
                    }
                }
            }
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
        $pdfContent = $this->generatePdf($berita_acara, $tgl_cetak_formatted);

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

    public function generatePdf($berita_acara, $tgl_cetak_formatted)
    {
        $pdf = new Fpdi();
        // Set the PDF to A4 size layout
        $pdf->AddPage('P', 'A4');

        // Set font and add content based on jenis
        if ($berita_acara->jenis === 'rusak') {
            $this->pdfRusak($pdf, $berita_acara, $tgl_cetak_formatted);
        } else {
            $this->pdfKeluarMasuk($pdf, $berita_acara, $tgl_cetak_formatted);
        }

        return $pdf->Output('S');
    }

    private function pdfRusak($pdf, $berita_acara, $tgl_cetak_formatted)
    {
        // Load the template
        $templatePath = storage_path('app/public/templates/KOP_Baru.pdf');
        $pdf->setSourceFile($templatePath);
        $templateId = $pdf->importPage(1);

        // Use the template on the first page
        $pdf->useTemplate($templateId, 0, 0, 210);
        $pdf->Ln(20);

        // Title
        $pdf->SetFont('Arial', 'BU', 16);
        $pdf->Cell(0, 6, 'BERITA ACARA', 0, 1, 'C');

        // Nomor
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(0, 6, 'Nomor: ' . $berita_acara->nomor_berita_acara, 0, 1, 'C');

        // Perihal
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 6, 'Perihal: ' . $berita_acara->perihal, 0, 1, 'C');
        $pdf->Ln(6); // Adjusted for tighter spacing

        // Keterangan
        $pdf->SetFont('Arial', '', 12);
        $pdf->MultiCell(0, 6, $berita_acara->keterangan);
        $pdf->Ln(6); // Adjusted for tighter spacing

        // Table header
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(10, 6, 'No', 1, 0, 'C');
        $pdf->Cell(45, 6, 'Nama Aset', 1, 0, 'C');
        $pdf->Cell(45, 6, 'Jumlah', 1, 0, 'C');
        $pdf->Cell(45, 6, 'S/N', 1, 0, 'C');
        $pdf->Cell(45, 6, 'Barcode', 1, 0, 'C');
        $pdf->Ln();

        // Table content
        $pdf->SetFont('Arial', '', 12);
        foreach ($berita_acara->assets as $index => $asset) {
            $pdf->Cell(10, 6, $index + 1, 1);
            $pdf->Cell(45, 6, $asset->nama_aset, 1);
            $pdf->Cell(45, 6, $asset->jumlah_fisik, 1);
            $pdf->Cell(45, 6, $asset->serial_number, 1);
            $pdf->Cell(45, 6, '-', 1);
            $pdf->Ln();
        }
        $pdf->Ln(8); // Adjusted for tighter spacing

        // Final text
        $pdf->MultiCell(0, 6, 'Demikian Berita Acara ini dibuat dengan sebenarnya untuk dapat dipergunakan sebagaimana mestinya.');
        $pdf->Ln(15); // Adjusted for tighter spacing

        // Signatures
        $pdf->SetFont('Arial', '', 12);
        $pdf->SetX(-105); // Move to the appropriate position
        $pdf->Cell(90, 10, 'Balikpapan, ' . $tgl_cetak_formatted, 0, 0, 'C');
        $pdf->Ln(10);
        $pdf->SetX(40); // Move slightly to the right
        $pdf->Cell(90, 10, 'Dibuat Oleh,', 0, 0, 'L');
        $pdf->SetX(-75); // Move slightly to the left
        $pdf->Cell(90, 10, 'Diketahui Oleh,', 0, 0, 'L');
        $pdf->Ln(30);

        // Adjusted position for Pihak Pertama
        $pdf->SetFont('Arial', 'BU', 12); // Set font to bold for pihak pertama
        $pdf->SetX(7); // Move slightly to the right
        $pdf->Cell(90, 10, $berita_acara->pihak_pertama, 0, 0, 'C');
        $pdf->Ln(5);
        $pdf->SetFont('Arial', '', 12); // Revert font to regular for jabatan pertama
        $pdf->SetX(7); // Move slightly to the right
        $pdf->Cell(90, 10, $berita_acara->jabatan_pertama, 0, 0, 'C');

        // Adjusted position for Pihak Kedua
        $pdf->SetFont('Arial', 'BU', 12); // Revert font to regular for jabatan pertama
        $pdf->SetY($pdf->GetY() - 5); // Set the same height as Pihak Pertama
        $pdf->SetX(-105); // Move slightly to the left
        $pdf->Cell(90, 10, "Muchammad Abdul Muiz", 0, 0, 'C');
        $pdf->Ln(5);
        $pdf->SetFont('Arial', '', 12); // Revert font to regular for jabatan pertama
        $pdf->SetX(-105); // Move slightly to the left
        $pdf->Cell(90, 10, 'Staff Equipment & Technology', 0, 0, 'C');

        $pdf->Ln(30); // Adjusted for tighter spacing

        $pdf->Cell(0, 10, 'Disetujui Oleh,', 0, 1, 'C');
        $pdf->Ln(20); // Adjusted for tighter spacing
        $pdf->SetFont('Arial', 'BU', 12); // Revert font to regular for jabatan pertama
        $pdf->Cell(0, 5, 'Syamsul Maarif', 0, 1, 'C');
        $pdf->SetFont('Arial', '', 12); // Revert font to regular for jabatan pertama
        $pdf->Cell(0, 5, 'Non Account Management', 0, 1, 'C');

        // New page for lampiran
        $pdf->AddPage('P', 'A4');

        // Use the template on the new page
        $pdf->useTemplate($templateId, 0, 0, 210);
        $pdf->Ln(20);

        // Title for lampiran
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 8, 'LAMPIRAN', 0, 1, 'C');

        // Nomor
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(0, 8, 'Nomor: ' . $berita_acara->nomor_berita_acara, 0, 1, 'C');

        // Description
        $pdf->Cell(0, 8, '(Foto barang yang rusak)', 0, 1, 'L');

        $pdf->Ln(6); // Adjusted for tighter spacing
        $pdf->SetFont('Arial', 'BU', 12);
        foreach ($berita_acara->assets as $asset) {
            $pdf->Cell(0, 8, $asset->nama_aset, 0, 1, 'C');
        }

        // Photos
        if ($berita_acara->images->isNotEmpty()) {
            $pdf->SetFont('Arial', '', 12);
            $imageWidth = 90; // Half of the page width to fit two images in a row
            $x = $pdf->GetX();
            $y = $pdf->GetY();
            foreach ($berita_acara->images as $index => $image) {
                if ($index % 2 == 0 && $index != 0) {
                    $pdf->Ln($imageWidth); // Move to next row after every two images
                    $x = $pdf->GetX();
                    $y = $pdf->GetY();
                }
                $pdf->Image(storage_path('app/' . $image->path), $x + ($index % 2) * $imageWidth, $y, $imageWidth);
            }
        }
    }

    private function pdfKeluarMasuk($pdf, $berita_acara, $tgl_cetak_formatted)
    {
        // Add some space for header
        $pdf->Ln(15);

        // Title
        $pdf->SetFont('Arial', 'BU', 16);
        $pdf->Cell(0, 10, 'BERITA ACARA KELUAR / MASUK BARANG GUDANG', 0, 1, 'C');
        $pdf->Ln(5);

        // Details section
        $pdf->SetFont('Arial', '', 12);
        $details = [
            'Nomor' => $berita_acara->nomor_berita_acara,
            'Perihal' => $berita_acara->perihal,
            'Lokasi' => $berita_acara->lokasi,
            'Hari/Tanggal' => $tgl_cetak_formatted,
        ];

        foreach ($details as $label => $value) {
            $pdf->Cell(40, 6, $label, 0, 0);
            $pdf->Cell(0, 6, ': ' . $value, 0, 1);
        }
        $pdf->Ln(3);

        // Divider
        $pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY());
        $pdf->Ln(5);

        // Pihak Pertama
        $pdf->Cell(0, 6, 'Yang bertanda tangan di bawah ini:', 0, 1);
        $pdf->Cell(40, 6, 'Nama', 0, 0);
        $pdf->Cell(0, 6, ': ' . $berita_acara->pihak_pertama, 0, 1);
        $pdf->Cell(40, 6, 'Jabatan', 0, 0);
        $pdf->Cell(0, 6, ': ' . $berita_acara->jabatan_pertama, 0, 1);
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 6, 'Dalam hal ini disebut sebagai pihak Pertama', 0, 1);
        $pdf->Ln(3);

        // Pihak Kedua
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(40, 6, 'Nama', 0, 0);
        $pdf->Cell(0, 6, ': ' . $berita_acara->pihak_kedua, 0, 1);
        $pdf->Cell(40, 6, 'Jabatan', 0, 0);
        $pdf->Cell(0, 6, ': ' . $berita_acara->jabatan_kedua, 0, 1);
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 6, 'Dalam hal ini disebut sebagai pihak Kedua', 0, 1);
        $pdf->Ln(3);

        // Menerangkan text
        $pdf->SetFont('Arial', '', 12);
        $pdf->MultiCell(0, 6, 'Dengan ini menerangkan bahwa Pihak Pertama telah melakukan pengambilan barang Operasional ICT berupa:');
        $pdf->Ln(3);

        // Table header
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(10, 8, 'No', 1, 0, 'C');
        $pdf->Cell(60, 8, 'Nama Aset', 1, 0, 'C');
        $pdf->Cell(60, 8, 'Serial Number', 1, 0, 'C');
        $pdf->Cell(60, 8, 'Kondisi', 1, 0, 'C');
        $pdf->Ln();

        // Table content
        $pdf->SetFont('Arial', '', 12);
        foreach ($berita_acara->assets as $index => $asset) {
            $pdf->Cell(10, 8, $index + 1, 1);
            $pdf->Cell(60, 8, $asset->nama_aset, 1);
            $pdf->Cell(60, 8, $asset->serial_number, 1);
            $pdf->Cell(60, 8, $asset->kondisi, 1);
            $pdf->Ln();
        }
        $pdf->Ln(8);

        // Final text
        $pdf->MultiCell(0, 6, 'Demikian Berita Acara ini dibuat dengan sebenarnya untuk dapat dipergunakan sebagaimana mestinya.');
        $pdf->Ln(15);

        // Signatures
        $pdf->SetFont('Arial', '', 12);
        $pdf->SetX(10); // Move slightly to the right
        $pdf->Cell(90, 10, 'Pihak Pertama', 0, 0, 'C');
        $pdf->SetX(-110); // Move slightly to the left
        $pdf->Cell(90, 10, 'Pihak Kedua', 0, 0, 'C');
        $pdf->Ln(30);

        // Adjusted position for Pihak Pertama
        $pdf->SetFont('Arial', 'U', 12);
        $pdf->SetX(10); // Move slightly to the right
        $pdf->Cell(90, 10, $berita_acara->pihak_pertama, 0, 0, 'C');
        $pdf->Ln(5);
        $pdf->SetFont('Arial', '', 12);
        $pdf->SetX(10); // Move slightly to the right
        $pdf->Cell(90, 10, $berita_acara->jabatan_pertama, 0, 0, 'C');

        // Adjusted position for Pihak Kedua
        $pdf->SetFont('Arial', 'U', 12);
        $pdf->SetY($pdf->GetY() - 5); // Set the same height as Pihak Pertama
        $pdf->SetX(-110); // Move slightly to the left
        $pdf->Cell(90, 10, $berita_acara->pihak_kedua, 0, 0, 'C');
        $pdf->Ln(5);
        $pdf->SetFont('Arial', '', 12);
        $pdf->SetX(-110); // Move slightly to the left
        $pdf->Cell(90, 10, $berita_acara->jabatan_kedua, 0, 0, 'C');
    }
}
