@extends('layout.layout')

@push('style')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.bootstrap5.css">
@endpush

@section('content')
<div class="container-fluid mt-3">
    <div class="card mb-3 border-0 shadow-sm" style="background-color:#f2f2f2;">
        <div class="card-body" style="background-color: #37B7C3; border-radius: 8px">
            <h2 class="m-0" style="color: #EBF4F6">Peserta Didik</h2>
        </div>
    </div>

    <!-- Import Modal -->
    <div class="modal fade" data-bs-backdrop="static" tabindex="-1" aria-hidden="true" id="excelModal">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Impor Data Siswa dari Excel</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('siswa.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="m-3">
                        <input type="file" name="file" class="form-control" accept=".xlsx" required>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-success">Impor</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    
    <!-- Import Button -->
    <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#excelModal" style="width: 6rem">Impor</button>
    <!-- Ekspor Button -->
    <a target="_blank" href="{{ route('siswa.export') }}" class="btn btn-secondary mb-3 px-3" style="width: 6rem">Ekspor</a>
    <!-- Tambah Button -->
    <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#createSiswaModal" style="width: 6rem">Tambah</button>

    {{-- toggle to enable "Edit" and "Delete" buttons --}} 
    <div class="form-check form-switch">
        <input class="form-check-input" type="checkbox" role="switch" id="flexSwitchCheckDefault" checked>  
        <label class="form-check-label" for="flexSwitchCheckDefault">Mode Edit</label>
    </div>


    <!-- Data Table -->
    <table id="example" class="table table-striped" style="width:100%">
        <thead>
            <tr>
                <th class="text-start">No</th>
                <th class="text-start">Nama</th>
                <th class="text-start">NISN</th>
                <th class="text-start">Jenis Kelamin</th>
                <th class="text-start">Angkatan</th>
                <th class="text-start">Agama</th>
                <!-- <th>Alamat</th> -->
                <th>Aksi</th>
                <th>Akun</th>
            </tr>
        </thead>
        <tbody>
            @foreach($siswas as $siswa)
                <tr>
                    <td class="text-start">{{ $loop->iteration }}</td>
                    <td class="text-start">{{ $siswa->nama }}</td>
                    <td class="text-start">{{ $siswa->nisn }}</td>
                    <td class="text-start">{{ $siswa->jenis_kelamin }}</td>
                    <td class="text-start">{{ $siswa->angkatan }}</td>
                    <td class="text-start">{{ $siswa->agama ?? ' - ' }}</td>
                    <td>
                        <!-- View Button to trigger modal -->
                        <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#viewSiswaModal-{{ $siswa->id }}" >
                            <i class="fa-solid fa-eye"></i>
                        </button>
                        
                        <!-- Edit Button to trigger modal -->
                        <button type="button" class="btn btn-warning controlled" data-bs-toggle="modal" data-bs-target="#editSiswaModal-{{ $siswa->id }}" >
                            <i class="fa-solid fa-edit"></i>
                        </button>

                        <!-- Delete Form -->
                        <form action="{{ route('siswa.delete', $siswa->id) }}" method="POST" class="d-inline delete-form" id="deleteForm-{{ $siswa->id }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger delete-button deleteAlert controlled" data-siswa-id="{{ $siswa->id }}" aria-label="Hapus Siswa" >
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </form>
                    </td>
                    <td>
                        <!-- Generate Single User Form -->
                        @if(empty($siswa->id_user))
                            <!-- Button to open the generate user modal -->
                                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#generateUserModal-{{ $siswa->id }}">
                                    <i class="fa-solid fa-plus"></i>
                                </button>
                                @include('siswa.generate')
                        @else
                            @role('Super Admin')
                                <a href="{{ route('account.index') }}">Lihat</a>
                            @endrole
                            @role('Admin')
                                <span>Sudah Ada</span>
                            @endrole
                        @endif
                    </td>
                </tr>
                @include('siswa.view')
                @include('siswa.update')
            @endforeach
            @include('siswa.create')
        </tbody>
    </table>
</div>
@endsection

@push('script')
    
@if(session('success'))
<!-- success alert -->
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
<script src="https://code.jquery.com/jquery-3.7.1.js"></script>
{{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script> --}}
<script src="https://cdn.datatables.net/2.1.8/js/dataTables.js"></script>
<script src="https://cdn.datatables.net/2.1.8/js/dataTables.bootstrap5.js"></script>
@if($errors->hasAny(['username','password','email']))
    <!-- success alert -->
    <script>
        Swal.fire({
            title: "Gagal! Mohon periksa kembali data yang digunakan",
            text: "Mohon periksa kembali data yang digunakan",
            icon: "error",
            timer: 5000, // Waktu dalam milidetik (3000 = 3 detik)
            showConfirmButton: false
        });
    </script>
@endif
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

        // Enable Edit and Delete buttons when toggle is checked
            $('#flexSwitchCheckDefault').on('change', function() {
                const isEditMode = this.checked;

                // Enable or disable all controlled buttons
                document.querySelectorAll('.controlled').forEach(button => {
                    button.disabled = !isEditMode;
                });
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