<?php

namespace App\Exports;

use App\Models\Siswa;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class KelasExport implements FromQuery, WithHeadings, WithColumnFormatting, ShouldAutoSize
{
    /**
    * @return \Illuminate\Support\Collection
    */
    use Exportable;

    private int $kelas;


    public function __construct(int $kelas)
    {
        $this->kelas = $kelas;
    }

    public function query() {
        return Siswa::query()->whereHas('kelases', function($query) {
            return $query->where('kelas_id', $this->kelas);  
        })->select('nama', DB::raw("CONCAT(nisn, ' ') as nisn"),'jenis_kelamin','angkatan');
    }

    public function headings() : array
        {
            return [
                'Nama',
                'NISN',
                'Jenis Kelamin',
                'Angkatan',
            ];
        }

    public function columnFormats(): array
        {
            return [
                'B' => NumberFormat::FORMAT_TEXT,
            ];
        }
}
