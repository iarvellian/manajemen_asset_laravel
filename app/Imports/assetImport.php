<?php

namespace App\Imports;

use App\Models\asset;
use App\Models\refLokasi;
use App\Models\refKelasAset;
use App\Models\refKodeProjek;
use App\Models\refDivisi;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;

HeadingRowFormatter::default('none');

class assetImport implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // Log the row to see its structure
        Log::info('Importing row: ', $row);

        // Check and add new entries for lokasi, asset_class, kode_project_new if they don't exist
        $lokasi = $this->findOrCreateLokasi($row['Lokasi']);
        $assetClass = $this->findOrCreateAssetClass($row['Asset Class']);
        $kodeProject = $this->findOrCreateKodeProject($row['Kode Project New']);

        return new Asset([
            'id_divisi' => $this->getDivisiId($row['Divisi'] ?? 'Default Divisi'),
            'id_lokasi' => $lokasi->id_lokasi,
            'id_kelas_aset' => $assetClass->id_kelas_aset,
            'id_kode_projek' => $kodeProject->id_kode_projek,
            'is_luar_kota' => $row['is_luar_kota'] ?? 0,
            'thn_perolehan' => $row['THN PEROLEHAN'],
            'cost_center' => $row['Cost center'],
            'ue' => $row['UE'],
            'kode_aset' => $row['Kode Aset'],
            'nama_aset' => $row['Nama Aset'],
            'jumlah_sap' => $row['Jmlh SAP'],
            'jumlah_fisik' => $row['Jmlh Fisik'],
            'kondisi' => $row['Kondisi'],
            'pic_aset' => $row['PIC Aset'],
            'pic_project' => $row['User/PIC Project'],
            'serial_number' => $row['Serial Number'],
            'no_rangka_kendaraan' => $row['No.Rangka Kendaraan'],
            'no_mesin_kendaraan' => $row['No.Mesin Kendaraan'],
            'no_plat_kendaraan' => $row['Plat Nomor Kendaraan'],
        ]);
    }

    /**
     * Find or create a new Lokasi entry.
     *
     * @param string $namaLokasi
     * @return \App\Models\refLokasi
     */
    private function findOrCreateLokasi($namaLokasi)
    {
        $lokasi = refLokasi::where('nama_lokasi', $namaLokasi)->first();

        if (!$lokasi) {
            $lokasi = refLokasi::create(['nama_lokasi' => $namaLokasi]);
            Log::info('Created new Lokasi: ' . $namaLokasi);
        }

        return $lokasi;
    }

    /**
     * Find or create a new AssetClass entry.
     *
     * @param string $namaAssetClass
     * @return \App\Models\refKelasAset
     */
    private function findOrCreateAssetClass($namaAssetClass)
    {
        $assetClass = refKelasAset::where('nama_kelas_aset', $namaAssetClass)->first();

        if (!$assetClass) {
            $assetClass = refKelasAset::create(['nama_kelas_aset' => $namaAssetClass]);
            Log::info('Created new Asset Class: ' . $namaAssetClass);
        }

        return $assetClass;
    }

    /**
     * Find or create a new KodeProject entry.
     *
     * @param string $namaKodeProject
     * @return \App\Models\refKodeProjek
     */
    private function findOrCreateKodeProject($namaKodeProject)
    {
        $kodeProject = refKodeProjek::where('nama_kode_projek', $namaKodeProject)->first();

        if (!$kodeProject) {
            $kodeProject = refKodeProjek::create(['nama_kode_projek' => $namaKodeProject]);
            Log::info('Created new Kode Project: ' . $namaKodeProject);
        }

        return $kodeProject;
    }

    /**
     * Get the ID for the divisi, set default or handle missing value.
     *
     * @param string|null $divisi
     * @return int|null
     */
    private function getDivisiId($divisi)
    {
        if ($divisi) {
            // Lookup divisi ID
            $divisiModel = refDivisi::where('nama_divisi', $divisi)->first();
            return $divisiModel ? $divisiModel->id_divisi : 1; // Default to 1 if not found
        } else {
            // Return default value
            return 1;
        }
    }
}
