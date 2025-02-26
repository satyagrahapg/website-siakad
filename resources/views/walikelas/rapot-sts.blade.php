<!-- style fixed -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="{{asset('style/assets/logo-sekolah.png')}}">
    <title>RAPOR TENGAH SEMESTER_{{ strtoupper($studentName)."_".$nisn }}</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        th,
        td {
            border: 1px solid black;
            text-align: left;
            padding: 8px;
        }

        th {
            text-align: center;
            background-color: #f2f2f2;
        }

        .table-title {
            font-weight: bold;
            text-align: left;
            padding: 8px;
            background-color: #e9e9e9;
        }

        .no-border {
            border: none;
        }

        .text-center {
            text-align: center;
        }

        .signature-table td {
            border: none;
            padding: 3px;
        }

        tr.komentar td.text-center {
            padding: 25px;
        }

        .rangkasurat {
            margin: 0 auto;
            padding: 20px;
        }

        table.rangkasurat td {
            border: none;
            border-bottom: 5px solid #000;
        }

        .tengah {
            text-align: center;
        }

        .judul {
            text-decoration: underline;
        }

        .page-break {
            page-break-before: always;
        }
    </style>
</head>

<body>
    <table class="rangkasurat" width="100%">
        <tr>
            <td class="tengah">
                <h2>PEMERINTAH KABUPATEN DEMAK</h2>
                <h2>DINAS PENDIDIKAN DAN KEBUDAYAAN</h2>
                <h2>SEKOLAH MENENGAH PERTAMA NEGERI 1 KARANGAWEN</h2>
                <b>
                    <span>Jl. Raya Karangawen No: 105 Demak</span>
                    <span>Telp : 024-76719044</span>
                    <br>
                    <span>NPSN : 20319344</span>
                    <span>NSS : 201032102005</span>
                </b>
            </td>
        </tr>
    </table>

    <!-- Data Siswa -->
    <table class="signature-table" style="margin-top: 15px;">
        <tr>
            <td>
                Nama
            </td>
            <td>
                : {{$studentName}}
            </td>
            <td>
                Kelas
            </td>
            <td>
                : {{$rombelData}}
            </td>
        </tr>
        <tr>
            <td>
                NISN
            </td>
            <td>
                : {{$nisn}}
            </td>
            <td>
                Semester
            </td>
            <td>
                : {{ substr($semester, -1) }}
            </td>
        </tr>
        <tr>
            <td>
                Nama Sekolah
            </td>
            <td>
                : SMP Negeri 1 Karangawen
            </td>
            <td>
                Tahun Ajaran
            </td>
            <td>
                : {{$tahunAjaran}}
            </td>
        </tr>
    </table>

    <h2 class="text-center judul" style="margin-top: 25px; margin-bottom: 25px;">LAPORAN HASIL BELAJAR TENGAH SEMESTER</h2>
    <h3>A. Mata Pelajaran</h3>
    <table>
        <thead>
            <tr>
                <th>No.</th>
                <th>Mata Pelajaran</th>
                <th>Nilai</th>
                <th>Capaian Kompetensi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($subjects as $subject => $grade)
            <tr>
                <td class="text-center">{{$loop->iteration}}</td>
                <td>{{ $subject }}</td>
                <td class="text-center">{{ round($grade, 0, PHP_ROUND_HALF_UP) }}</td>
                <td>
                    Ananda {{$studentName}} telah menguasai
                    @if (!empty($komentarRapot[$subject]))
                        @php
                            $Comment = $komentarRapot[$subject];
                            $jumlahComment = count($Comment);
                            $formattedComment = '';

                            if ($jumlahComment > 2) {
                                $formattedComment = implode(', ', array_slice($Comment, 0, -1)) . ', serta ' . end($Comment);
                            } else if ($jumlahComment == 2) {
                                $formattedComment = implode(', ', array_slice($Comment, 0, -1)) . ' serta ' . end($Comment);
                            } else {
                                $formattedComment = $Comment[0];
                            }
                            $formattedComment .= '.';
                        @endphp
                        {{ $formattedComment }}
                    @else
                        <span></span>
                    @endif

                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <h3>B. Ketidakhadiran</h3>
    <table>
        <thead>
            <tr>
                <th width="60%">Keterangan</th>
                <th>Jumlah</th>
            </tr>
        </thead>
        <tbody>
            @php
                $absensi = [];
                foreach ($absensiSummary as $record) {
                    $absensi[$record->status] = $record->count;
                }
            @endphp
            <tr>
                <td>Terlambat
                <td>{{ $absensi['terlambat'] ?? 0 }}</td>
            </tr>
            <tr>
                <td>Izin</td>
                <td>{{ $absensi['ijin'] ?? 0 }}</td>
            </tr>
            <tr>
                <td>Alpa</td>
                <td>{{ $absensi['alpha'] ?? 0 }}</td>
            </tr>
            <tr>
                <td>Sakit</td>
                <td>{{ $absensi['sakit'] ?? 0 }}</td>
            </tr>
        </tbody>
    </table>


    <h3 style="margin-top: 40px;">Catatan Wali Kelas</h3>
    <table>
        <tr class="komentar">
            <td class="text-center">{{ $komentar ?: 'Tidak ada komentar dari wali kelas.' }}</td>
        </tr>
    </table>

    <table class="signature-table" style="margin-top: 50px; width: 100%;">
        <tr>
            <td width="35%"><span style="color: white;">Mengetahui</span><br>Orang Tua/Wali,</td>
            <td width="30%"></td>
            @php
                use Carbon\Carbon;
                Carbon::setLocale('id');
                $tanggalSekarang = Carbon::now()->isoFormat('D MMMM YYYY');
            @endphp
            <td width="40%">Demak, {{ $tanggalSekarang }}<br>Wali Kelas,</td>
        </tr>
        <tr>
            <td style="padding-top: 70px;">..................................<br><span style="color: white">Space</span></td>
            <td></td>
            <td style="padding-top: 70px;"><span style="text-decoration: underline; font-weight: bold;">{{ $ttd["walikelas"] }}</span><br>NIP. {{ $ttd["nip_walikelas"] }}</td>
        </tr>
    </table>
</body>

</html>