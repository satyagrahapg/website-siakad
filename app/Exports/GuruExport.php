<?php

namespace App\Exports;

use App\Models\Guru;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

    class GuruExport implements FromQuery, WithHeadings, WithColumnFormatting, ShouldAutoSize
    {
        /**
        * @return \Illuminate\Support\Collection
        */
        public function query() {
            return Guru::query()->select('nama',  DB::raw("CONCAT(nip, ' ') as nip"), 'tempat_lahir', 'tanggal_lahir', 'jenis_kelamin', 'agama', 'alamat', 'jabatan', 'status', 'pangkat_golongan', 'pendidikan');
        }

        public function headings() : array
        {
            return [
                'Nama',
                'NIP',
                'Tempat Lahir',
                'Tanggal Lahir',
                'Jenis Kelamin',
                'Agama',
                'Alamat',
                'Jabatan',
                'Status',
                'Pangkat Golongan',
                'Pendidikan',
            ];
        }

        public function columnFormats(): array
        {
            return [
                'B' => NumberFormat::FORMAT_TEXT ,
            ];
        }
    }
