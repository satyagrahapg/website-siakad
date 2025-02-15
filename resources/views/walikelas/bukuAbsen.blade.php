@extends('layout.layout')
@push('style')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.bootstrap5.css">
@endpush
@section('content')
<div class="container-fluid mt-3">
    <div class="card mb-3 border-0 shadow-sm" style="background-color:#f2f2f2;">
        <div class="card-body" style="background-color: #37B7C3; border-radius: 8px">
            <h2 class="m-0" style="color: #EBF4F6">Buku Presensi {{ optional($kelasSemester)->rombongan_belajar ? '| '.$kelasSemester->rombongan_belajar: '' }}</h2>
        </div>
    </div>

    <table id="example" class="table table-striped" style="width:100%">
        <thead>
            <tr>
                <th class="text-start" width="5%">No</th>
                <th class="text-start">Nama</th>
                <th class="text-start">NISN</th>
                <th class="text-start">Hadir</th>
                <th class="text-start">Terlambat</th>
                <th class="text-start">Izin</th>
                <th class="text-start">Sakit</th>
                <th class="text-start">Alpa</th>
                <th class="text-start">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($students as $student)
            <tr>
                <td class="text-start">{{ $loop->iteration }}</td>
                <td class="text-start">{{ $student->nama }}</td>
                <td class="text-start">{{ $student->nisn }}</td>
                <td class="text-start">{{ $student->count_hadir }}</td>
                <td class="text-start">{{ $student->count_terlambat }}</td>
                <td class="text-start">{{ $student->count_ijin }}</td>
                <td class="text-start">{{ $student->count_sakit }}</td>
                <td class="text-start">{{ $student->count_alpha }}</td>
                <td class="text-start">{{ $student->count_all }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
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