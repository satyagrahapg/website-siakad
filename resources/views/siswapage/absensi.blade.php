@extends('layout.layout')

@push('style')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.bootstrap5.css">
@endpush

@section('content')
<div class="container-fluid mt-3">
    <div class="card mb-3 border-0 shadow-sm" style="background-color:#f2f2f2;">
        <div class="card-body" style="background-color: #37B7C3; border-radius: 8px">
            <h2 class="m-0" style="color: #EBF4F6">Presensi Peserta Didik</h2>
        </div>
    </div>

    <div class="row mb-3">
        <h3>Kelas : {{ $dataKelas['nama'] ?? '-' }}</h3>
        <h3>Wali Kelas : {{ $dataKelas['nama_guru'] ?? '-' }}</h3>
    </div>

    <div class="row mb-3">
        <div class="col">
            <div class="card text-center">
                <div class="card-header bg-success text-white">
                    <h5>Hadir</h5>
                </div>
                <div class="card-body">
                    <h3>{{ $absensi['hadir'] ?? 0 }}</h3>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card text-center">
                <div class="card-header bg-primary text-white">
                    <h5>Sakit</h5>
                </div>
                <div class="card-body">
                    <h3>{{ $absensi['sakit'] ?? 0 }}</h3>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card text-center">
                <div class="card-header bg-warning text-white">
                    <h5>Terlambat</h5>
                </div>
                <div class="card-body">
                    <h3>{{ $absensi['terlambat'] ?? 0 }}</h3>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card text-center">
                <div class="card-header bg-info text-white">
                    <h5>Izin</h5>
                </div>
                <div class="card-body">
                    <h3>{{ $absensi['ijin'] ?? 0 }}</h3>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card text-center">
                <div class="card-header bg-danger text-white">
                    <h5>Alpa</h5>
                </div>
                <div class="card-body">
                    <h3>{{ $absensi['alpha'] ?? 0 }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Attendance Data Table -->
    <div class="card">
        <div class="card-header">
            <h5>Riwayat Presensi</h5>
        </div>
        <div class="card-body">
            <table id="example" class="table table-striped" style="width:100%">
                <thead>
                    <tr>
                        <th class="text-start">No</th>
                        <th class="text-start">Tanggal</th>
                        <th class="text-start">Terakhir Diperbarui</th>
                        <th class="text-start">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($dataAbsensi as $index => $absensi)
                    <tr>
                        <td class="text-start">{{ $loop->iteration }}</td>
                        <td class="text-start">{{ $absensi->date }}</td>
                        <td class="text-start">{{ Carbon\Carbon::parse($absensi->updated_at)->addHours(7)->format('Y-m-d H:i:s') }}</td>
                        <td class="text-start">
                            <span class="badge 
                                    @if($absensi->status == 'sakit') bg-primary 
                                    @elseif($absensi->status == 'hadir') bg-success 
                                    @elseif($absensi->status == 'terlambat') bg-warning 
                                    @elseif($absensi->status == 'ijin') bg-info 
                                    @elseif($absensi->status == 'alpha') bg-danger 
                                    @endif">
                                @if ($absensi->status === 'sakit')
                                    {{ 'Sakit' }}
                                @elseif ($absensi->status === 'hadir')
                                    {{ 'Hadir' }}
                                @elseif ($absensi->status === 'terlambat')
                                    {{ 'Terlambat' }}
                                @elseif ($absensi->status === 'ijin')
                                    {{ 'Izin' }}
                                @elseif ($absensi->status === 'alpha')
                                    {{ 'Alpa' }}
                                @endif
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="text-center">No attendance data found.</td>
                        <td style="display: none;"></td>
                        <td style="display: none;"></td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('script')
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script> -->
    <script src="https://cdn.datatables.net/2.1.8/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.1.8/js/dataTables.bootstrap5.js"></script>
    <script>
        $(document).ready(function() {
            // Cek apakah DataTable sudah diinisialisasi
            if ($.fn.DataTable.isDataTable('#example')) {
                $('#example').DataTable().destroy(); // Hancurkan DataTable yang ada
            }

            // Inisialisasi DataTable dengan opsi
            $('#example').DataTable({
                language: {
                    url: "{{ asset('style/js/bahasa.json') }}" // Ganti dengan path ke file bahasa Anda
                }
            });
        });
    </script>
@endpush