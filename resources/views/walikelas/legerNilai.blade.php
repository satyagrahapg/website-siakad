@extends('layout.layout')

@push('style')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.bootstrap5.css">
<link rel="stylesheet" href="https://cdn.datatables.net/fixedcolumns/5.0.4/css/fixedColumns.dataTables.css">
<link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.dataTables.css">
<style>
    th,
    td {
        white-space: nowrap;
    }

    div.dataTables_wrapper {
        width: 800px;
        margin: 0 auto;
    }
</style>
@endpush

@section('content')
<div class="container-fluid my-3">
    <div class="card mb-3 border-0 shadow-sm" style="background-color:#f2f2f2;">
        <div class="card-body" style="background-color: #37B7C3; border-radius: 8px">
            <h2 class="m-0" style="color: #EBF4F6">Leger Nilai {{ optional($kelasSemester)->rombongan_belajar ? '| '.$kelasSemester->rombongan_belajar: '' }}</h2>
        </div>
    </div>

    <table id="example" class="table table-striped stripe row-border order-column" style="width:100%">
        <thead>
            <tr>
                <th class="text-start">No</th>
                <th class="text-start">Nama</th>
                <th class="text-start">NISN</th>
                <th class="text-start">Kelas</th>
                <th class="text-start">Tanggal Penilaian</th>
                @foreach ($subjects as $subject)
                    <th class="text-start">{{ $subject }}</th>
                @endforeach
                <th>Buat Rapor</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $index => $siswa)
            <tr>
                <td class="text-start">{{ $loop->iteration }}</td>
                <td class="text-start">{{ $siswa['all']['nama'] }}</td>
                <td class="text-start">{{ $siswa['all']['nisn'] }}</td>
                <td class="text-start">{{ $siswa['all']['kelas'] }}</td>
                <td class="text-start">{{ $siswa['all']['tanggal'] }}</td>
                @foreach ($subjects as $subject)
                    <td class="text-start">{{ number_format($siswa['all'][$subject] ?? 0, 2) }}</td>
                @endforeach
                <td class="text-start">
                    <!-- Modal Trigger Buttons -->
                    <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#buatRapotMid{{ $loop->index }}">
                        TS
                    </button>
                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#buatRapotSemester{{ $loop->index }}">
                        AS
                    </button>
                </td>
            </tr>

            <!-- Modal for Mid Rapot -->
            <div class="modal fade" id="buatRapotMid{{ $loop->index }}" tabindex="-1" aria-labelledby="buatRapotMidLabel{{ $loop->index }}" aria-hidden="true">
                <div class="modal-dialog modal-dialog-scrollable">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="buatRapotMidLabel{{ $loop->index }}">Rapot Tengah Semester - {{ $siswa['sts']['nama'] }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form action="{{ route('pesertadidik.generateRapot') }}" method="POST" class="m-0" target="blank">
                            <div class="modal-body">
                                @csrf
                                <input type="hidden" name="tipe_penilaian" value="sts">
                                <input type="hidden" name="tanggal_sts" value="{{ $siswa['sts']['tanggal'] }}">
                                <input type="hidden" name="student_name" value="{{ $siswa['sts']['nama'] }}">
                                <input type="hidden" name="student_id" value="{{ $index }}">
                                <input type="hidden" name="student_religion" value="{{ $siswa['sts']['agama'] }}">

                                @foreach ($subjects as $subject)
                                    <input type="hidden" name="subjects[{{ $subject }}]" value="{{ $siswa['sts'][$subject] ?? 0 }}">
                                @endforeach

                                <div class="mb-3">
                                    <label for="tanggal-{{ $loop->index }}" class="form-label">Tanggal Penilaian</label>
                                    <input type="text" class="form-control" id="tanggal-{{ $loop->index }}" value="{{ $siswa['sts']['tanggal'] }}" readonly disabled>
                                </div>

                                @foreach ($subjects as $subject)
                                    <div class="mb-3">
                                        <label for="subject-{{ $loop->index }}" class="form-label">{{ $subject }}</label>
                                        <input type="text" class="form-control" id="subject-{{ $loop->index }}" value="{{ $siswa['sts'][$subject] ?? 0 }}" readonly disabled>
                                    </div>
                                @endforeach

                                <!-- Komentar Text Area (fillable) -->
                                <div class="mb-3">
                                    <label for="komentar" class="form-label">Komentar Wali Kelas</label>
                                    <textarea class="form-control" id="komentar" name="komentar" rows="3">{{ old('komentar') }}</textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                <button type="submit" class="btn btn-primary">Unduh Rapor</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Modal for Semester Rapot -->
            <div class="modal fade" id="buatRapotSemester{{ $loop->index }}" tabindex="-1" aria-labelledby="buatRapotSemesterLabel{{ $loop->index }}" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="buatRapotSemesterLabel{{ $loop->index }}">Rapot Akhir Semester - {{ $siswa['sas']['nama'] }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form action="{{ route('pesertadidik.generateRapot') }}" method="POST" class="m-0" target="blank">
                            <div class="modal-body">
                                @csrf
                                <input type="hidden" name="tipe_penilaian" value="sas">
                                <input type="hidden" name="tanggal_sas" value="{{ $siswa['sas']['tanggal'] }}">
                                <input type="hidden" name="student_name" value="{{ $siswa['sas']['nama'] }}">
                                <input type="hidden" name="student_id" value="{{ $index }}">
                                <input type="hidden" name="student_religion" value="{{ $siswa['sas']['agama'] }}">

                                @foreach ($subjects as $subject)
                                    <input type="hidden" name="subjects[{{ $subject }}]" value="{{ $siswa['sas'][$subject] ?? 0 }}">
                                @endforeach

                                <div class="mb-3">
                                    <label for="tanggal-{{ $loop->index }}" class="form-label">Tanggal Penilaian</label>
                                    <input type="text" class="form-control" id="tanggal-{{ $loop->index }}" value="{{ $siswa['sas']['tanggal'] }}" readonly disabled>
                                </div>

                                @foreach ($subjects as $subject)
                                    <div class="mb-3">
                                        <label for="subject-semester-{{ $loop->index }}" class="form-label">{{ $subject }}</label>
                                        <input type="text" class="form-control" id="subject-semester-{{ $loop->index }}" value="{{ $siswa['sas'][$subject] ?? 0 }}" readonly disabled>
                                    </div>
                                @endforeach

                                <!-- Prestasi Fields (nullable, can be filled) -->
                                <div class="mb-3">
                                    <label for="prestasi_1" class="form-label">Prestasi 1</label>
                                    <input type="text" class="form-control" id="prestasi_1" name="prestasi_1" value="{{ old('prestasi_1') }}">
                                </div>
                                <div class="mb-3">
                                    <label for="prestasi_2" class="form-label">Prestasi 2</label>
                                    <input type="text" class="form-control" id="prestasi_2" name="prestasi_2" value="{{ old('prestasi_2') }}">
                                </div>
                                <div class="mb-3">
                                    <label for="prestasi_3" class="form-label">Prestasi 3</label>
                                    <input type="text" class="form-control" id="prestasi_3" name="prestasi_3" value="{{ old('prestasi_3') }}">
                                </div>

                                <!-- Komentar Text Area (fillable) -->
                                <div class="mb-3">
                                    <label for="komentar" class="form-label">Komentar Wali Kelas</label>
                                    <textarea class="form-control" id="komentar" name="komentar" rows="3">{{ old('komentar') }}</textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                <button type="submit" class="btn btn-primary">Unduh Rapor</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            @endforeach
        </tbody>
    </table>
</div>
@endsection

@push('script')
    
<!-- success alert -->
@if(session('success'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
            title: "Berhasil!",
            text: "{{ session('success') }}",
            icon: "success",
            timer: 1500, // Waktu dalam milidetik (3000 = 3 detik)
            showConfirmButton: false
        });
    });
</script>
@endif

<!-- error alert -->
@if(session('error'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
            title: "Gagal!",
            text: "{{ session('error') }}",
            icon: "error",
            timer: 1500, // Waktu dalam milidetik (1500 = 1.5 detik)
            showConfirmButton: false
        });
    });
</script>
@endif

<script src="https://code.jquery.com/jquery-3.7.1.js"></script>
{{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script> --}}
<script src="https://cdn.datatables.net/2.1.8/js/dataTables.js"></script>
<script src="https://cdn.datatables.net/2.1.8/js/dataTables.bootstrap5.js"></script>
<script src="https://cdn.datatables.net/fixedcolumns/5.0.4/js/fixedColumns.dataTables.js"></script>
<script src="https://cdn.datatables.net/fixedcolumns/5.0.4/js/dataTables.fixedColumns.js"></script>

<script>
    $(document).ready(function() {
        // Cek apakah DataTable sudah diinisialisasi
        if ($.fn.DataTable.isDataTable('#example')) {
            $('#example').DataTable().destroy(); // Hancurkan DataTable yang ada
        }

        // Inisialisasi DataTable dengan opsi
        $('#example').DataTable({
            fixedColumns: {
                start: 2,
                end: 1
            },
            paging:false,
            scrollCollapse: true,
            scrollX: true,
            scrollY: 500,
            language: {
                url: "{{ asset('style/js/bahasa.json') }}" // Ganti dengan path ke file bahasa Anda
            }
        });
    });
</script>
<script>
    document.querySelectorAll('.deleteAlert').forEach(function(button, index) {
        button.addEventListener('click', function(event) {
            event.preventDefault();
            Swal.fire({
                title: "Apakah Anda Yakin?",
                text: "Data Akan Dihapus Permanen dari Basis Data!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Ya, Hapus!",
                cancelButtonText: "Batal"
            }).then((result) => {
                // Jika konfirmasi "Ya, Hapus!" diklik
                if (result.isConfirmed) {
                    // Mengirim formulir untuk menghapus data
                    event.target.closest('form').submit();
                }
            });
        });
    });
</script>
@endpush