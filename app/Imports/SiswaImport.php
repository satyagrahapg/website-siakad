<?php

namespace App\Imports;

use App\Models\Siswa;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithUpserts;
use Maatwebsite\Excel\Concerns\WithSkipDuplicates;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\ToModel;

class SiswaImport implements ToModel, WithValidation, WithHeadingRow, WithUpserts, WithSkipDuplicates 
{
    /**
     * @param array $row
     *
     * @return Siswa|null
     */

    public function uniqueBy() {
        return 'no_pendaftaran';
    }

    public function rules(): array
    {
        return [
            'nama' => 'required',
            'nisn' => [
                'required',
                'unique:siswas,nisn',
                'regex:/^(?:\d{10})$/'
            ],
            'tanggal_lahir' => 'nullable',
            'tempat_lahir' => 'nullable',
            'jenis_kelamin' => 'nullable',
            'agama' => 'nullable',
            'status_keluarga' => 'nullable',
            'anak_ke' => 'nullable',
            'alamat' => 'nullable',
            'telepon' => 'nullable',
            'asal_sekolah' => 'nullable',
            'tanggal_diterima' => 'nullable',
            'jalur_penerimaan' => 'nullable',
            'nama_ayah' => 'nullable',
            'pekerjaan_ayah' => 'nullable',
            'nama_ibu' => 'nullable',
            'pekerjaan_ibu' => 'nullable',
            'nama_wali' => 'nullable',
            'pekerjaan_wali' => 'nullable',
            'angkatan' => 'required',
        ];
    }
    
    public function model(array $row)
    {
        // Return data kosong sebagai null data
        if (!array_filter($row)) {
            return null;
        }

        return new Siswa([
            'nama' => $row['nama'] ?? null,
            'nisn' => $row['nisn'] ?? null,
            'tanggal_lahir' => $row['tanggal_lahir'] ?? null,
            'tempat_lahir' => $row['tempat_lahir'] ?? null,
            'jenis_kelamin' => $row['jenis_kelamin'] ?? null,
            'agama' => $row['agama'] ?? null,
            'status_keluarga' => $row['status_keluarga'] ?? null,
            'anak_ke' => $row['anak_ke'] ?? null,
            'alamat' => $row['alamat'] ?? null,
            'telepon' => $row['telepon'] ?? null,
            'asal_sekolah' => $row['asal_sekolah'] ?? null,
            'tanggal_diterima' => $row['tanggal_diterima'] ?? null,
            'jalur_penerimaan' => $row['jalur_penerimaan'] ?? null,
            'nama_ayah' => $row['nama_ayah'] ?? null,
            'pekerjaan_ayah' => $row['pekerjaan_ayah'] ?? null,
            'nama_ibu' => $row['nama_ibu'] ?? null,
            'pekerjaan_ibu' => $row['pekerjaan_ibu'] ?? null,
            'nama_wali' => $row['nama_wali'] ?? null,
            'alamat_wali' => $row['alamat_wali'] ?? null,
            'pekerjaan_wali' => $row['pekerjaan_wali'] ?? null,
            'angkatan' => $row['angkatan'] ?? null,
        ]);
    }

    public function customValidationMessages()
    {
        return [
            'nisn.unique' => 'NISN telah terdaftar.',
            'nisn.regex' => 'NISN harus terdiri dari tepat 10 digit.'
        ];
    }
}
