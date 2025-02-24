@extends('layout.layout')

@push('style')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.bootstrap5.css">
@endpush

@section('content')
<div class="container-fluid mt-3">
    <div class="card mb-3 border-0 shadow-sm" style="background-color:#f2f2f2;">
        <div class="card-body" style="background-color: #37B7C3; border-radius: 8px">
            <h2 class="m-0" style="color: #EBF4F6">Ekstrakurikuler {{ $kelas->rombongan_belajar }}</h2>
        </div>
    </div>

    <!-- Add Student Modal Trigger -->
    <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addEkskulModal-{{ $kelas->id }}">Tambah</button>

    <!--Import & Export Student Modal Trigger -->
        <button class="btn btn-info mb-3" data-bs-toggle="modal"
            data-bs-target="#importStudentModal-{{ $kelas->id }}" style="width: 5rem">Impor</button>

    <table id="example" class="table table-striped" style="width:100%">
        <thead>
            <tr>
                <th class="text-start">No</th>
                <th class="text-start">Nama</th>
                <th class="text-start">NISN</th>
                <th class="text-start">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($daftar_siswa as $siswa)
            <tr>
                <td class="text-start">{{$loop-> iteration}}</td>
                <td class="text-start">{{ $siswa['nama'] }}</td>
                <td class="text-start">{{ $siswa['nisn'] }}</td>
                <td class="text-start">
                    <form action="{{ route('kelas.siswa.delete', ['kelasId' => $kelas->id, 'siswaId' => $siswa->id]) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this student from this class?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger deleteAlert">Hapus</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Add Student Modal -->
    <div class="modal fade" id="addEkskulModal-{{ $kelas->id }}" tabindex="-1" aria-labelledby="addEkskulModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('kelas.addStudent', $kelas->id) }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="addEkskulModalLabel">Tambah Peserta Didik ke {{ $kelas->rombongan_belajar }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="id_siswa" class="form-label">Pilih Peserta Didik</label>
                            <select name="id_siswa" class="form-select selectpicker" data-live-search="true" required>
                                <option value="" selected disabled hidden>Pilih Peserta Didik</option>
                                @foreach($siswas as $siswa)
                                <option value="{{ $siswa->id }}">{{ $siswa->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Tambah</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Import Student Modal --}}
        <div class="modal fade" id="importStudentModal-{{ $kelas->id }}" tabindex="-1"
            aria-labelledby="addStudentModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form
                        action="{{ route('kelas.importFromEkskul', ['ekskulId' => $kelas->id, 'angkatan' => request('angkatan')]) }}"
                        method="POST" style="display:inline;">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title" id="addStudentModalLabel">Impor Data Siswa</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="alert alert-info" role="alert">
                                Gunakan Fitur ini untuk mengimpor data siswa dari semester/kelas lain
                            </div>
                            <div class="mb-3">
                                <label for="semester">Pilih Semester:</label>
                                <select name="semester" id="semester"
                                    class="form-select @error('semester') is-invalid @enderror" id="selectsemester" required>
                                    <option value="" selected disabled hidden>-- Select semester --</option>
                                    @foreach ($semesters as $semester)
                                        <option value="{{ $semester->id }}" @selected(old('semester') == $semester->id)>
                                            {{ $semester->semester . ' | ' . $semester->tahun_ajaran }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="kelas">Pilih Kelas:</label>
                                <select name="kelas" id="kelas"
                                    class="form-select @error('kelas') is-invalid @enderror" id="selectKelas" required>
                                    <option value="" selected disabled hidden>Mohon pilih semester terlebih dahulu</option>
                                </select>
                                @error('kelas')
                                    <p class="invalid-feedback">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Impor Siswa</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
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
<script>
    const selectSemester = document.getElementById('semester');
    semester.addEventListener('change', function() {
        console.log(semester.value)
        $.ajax({
            url: "{{ route('kelas.getEkskul') }}",
            type: 'GET',
            data: {
                semesterId: selectSemester.value
            },
            success: function(response) {
                console.log(response);
                const selectKelas = document.getElementById('kelas');
                selectKelas.innerHTML = '<option value="">-- Select Ekskul --</option>';
                response.forEach(function(kelas) {
                    const option = document.createElement('option');
                    option.value = kelas.id;
                    option.textContent = kelas.rombongan_belajar;
                    selectKelas.appendChild(option);
                });
            }
        });
    });
</script>
@endpush