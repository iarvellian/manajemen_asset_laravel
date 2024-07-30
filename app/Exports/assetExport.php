<?php

namespace App\Exports;

use App\Models\asset;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;




class assetExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return asset::with('divisi', 'kodeProjek', 'kelasAset', 'lokasi')->get();
    }

    /**
     * Return the headings for the export.
     *
     * @return array
     */
    public function headings(): array
    {
        return [
            'Thn Perolehan',
            'Lokasi',
            'Cost Center',
            'UE',
            'Kode Aset',
            'Nama Aset',
            'Asset Classs',
            'Jmlh SAP',
            'Jmlh Fisik',
            'Kondisi',
            'Kode Project New',
            'PIC Aset',
            'User/PIC Project',
            'Serial Number',
            'No. Rangka Kendaraan',
            'No. Mesin Kendaraan',
            'Plat Nomor Kendaraan',
            'Divisi'
        ];
    }

    /**
     * Make heading row bold.
     *
     * @return array
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text.
            1    => ['font' => ['bold' => true]],
        ];
    }

    /**
     * Map the data for the export.
     *
     * @return array
     */
    public function map($asset): array
    {
        return [
            $asset->thn_perolehan,
            $asset->lokasi->nama_lokasi ?? '',
            $asset->cost_center,
            $asset->ue,
            $asset->kode_aset,
            $asset->nama_aset,
            $asset->kelasAset->nama_kelas_aset ?? '',
            $asset->jumlah_sap,
            $asset->jumlah_fisik,
            $asset->kondisi,
            $asset->kodeProjek->nama_kode_projek ?? '',
            $asset->pic_aset,
            $asset->pic_project,
            $asset->serial_number,
            $asset->no_rangka_kendaraan,
            $asset->no_mesin_kendaraan,
            $asset->no_plat_kendaraan,
            $asset->divisi->nama_divisi ?? '',
        ];
    }
}
