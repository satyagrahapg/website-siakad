<?php

namespace App\Imports;

use App\Models\Tendik;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithUpserts;
use Maatwebsite\Excel\Concerns\WithSkipDuplicates;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\ToModel;

class TendikImport implements ToModel, WithValidation, WithHeadingRow, WithUpserts, WithSkipDuplicates 
{
    /**
     * @param array $row
     *
     * @return Admin|null
     */

    public function uniqueBy() {
        return 'nip';
    }

    public function rules(): array
    {
        return [
            'nama' => 'required',
            'nip' => [
                'required',
                'unique:tendiks,nip',
                'regex:/^(?:\d{11}|\d{18})$/'
            ],
            'tempat_lahir' => 'nullable',
            'tanggal_lahir' => 'nullable|date',
            'jenis_kelamin' => 'nullable|in:Laki-laki,Perempuan',
            'agama' => 'nullable',
            'alamat' => 'nullable',
            'jabatan' => 'nullable',
            'status' => 'nullable',
            'pangkat_golongan' => 'nullable',
            'pendidikan' => 'nullable',
        ];
    }
    
    public function model(array $row)
    {
        // Return null if row data is empty
        if (!array_filter($row)) {
            return null;
        }

        return new Tendik([
            'nip' => $row['nip'] ?? null,
            'nama' => $row['nama'] ?? null,
            'tempat_lahir' => $row['tempat_lahir'] ?? null,
            'tanggal_lahir' => $row['tanggal_lahir'] ?? null,
            'jenis_kelamin' => $row['jenis_kelamin'] ?? null,
            'agama' => $row['agama'] ?? null,
            'alamat' => $row['alamat'] ?? null,
            'jabatan' => $row['jabatan'] ?? null,
            'status' => $row['status'] ?? null,
            'pangkat_golongan' => $row['pangkat_golongan'] ?? null,
            'pendidikan' => $row['pendidikan'] ?? null,
        ]);
    }

    public function customValidationMessages()
    {
        return [
            'nip.unique' => 'NIP atau Kode Pegawai telah terdaftar.',
            'nip.regex' => 'NIP atau Kode Pegawai harus terdiri dari tepat 11 atau 18 digit.'
        ];
    }
}
