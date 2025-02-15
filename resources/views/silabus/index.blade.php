@extends('layout.layout')

@push('style')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.bootstrap5.css">
@endpush

@section('content')
<div class="container-fluid mt-3">
    <div class="card mb-3 border-0 shadow-sm" style="background-color:#f2f2f2;">
        <div class="card-body" style="background-color: #37B7C3; border-radius: 8px">
            <h2 class="m-0" style="color: #EBF4F6">Silabus {{ $mapel->nama }} | Kelas {{ $mapel->kelas }}</h2>
        </div>
    </div>

    <!-- Button to Open Create CP Modal -->
    <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#createCPModal">
        Tambah CP
    </button>

    <!-- modal Informasi -->
    <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#infoCPModal">
        <i class="fa-solid fa-circle-info"></i> Informasi
    </button>

    <!-- toggle to enable "Edit" and "Delete" buttons  -->
    <div class="form-check form-switch">
        <input class="form-check-input" type="checkbox" role="switch" id="flexSwitchCheckDefault" checked>  
        <label class="form-check-label" for="flexSwitchCheckDefault">Mode Edit</label>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="infoCPModal" tabindex="-1" aria-labelledby="infoModalTP" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Informasi</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>CP : Capaian Pembelajaran</p>
                    <p>TP : Tujuan Pembelajaran</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Table of CPs -->
    <table id="example" class="table table-striped" style="width:100%">
        <thead>
            <tr>
                <th class="text-start">CP</th>
                <th>Topik</th>
                <th>Keterangan</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($cps as $cp)
            <tr>
                <td class="text-start">{{ $cp->nomor}}</td>
                <td>{{ $cp->nama }}</td>
                <td>{{ $cp->keterangan }}</td>
                <td>
                    <!-- Buat TP Button -->
                    <form action="{{ route('bukaTP', ['mapelId' => $mapelId, 'cpId' => $cp->id]) }}" method="GET" style="display: inline;" class="m-0">
                        <button type="submit" style="width: 5.5rem;" class="btn btn-primary">
                            Buka TP
                        </button>
                    </form>
                    <!-- Update Button -->
                    <button type="button" class="btn btn-warning controlled" data-bs-toggle="modal" data-bs-target="#updateCPModal-{{ $cp->id }}" class="m-0"><i class="fa-solid fa-pen-to-square"></i></button>

                    <!-- Delete Form -->
                    <form action="{{ route('silabus.deleteCP', [$mapelId, $cp->id]) }}" method="POST" style="display: inline-block;" class="m-0">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger deleteAlert controlled"><i class="fa-solid fa-trash"></i></button>
                    </form>
                </td>
            </tr>

            <!-- Update Modal for CP -->
            <div class="modal fade" id="updateCPModal-{{ $cp->id }}" tabindex="-1" aria-labelledby="updateCPModalLabel-{{ $cp->id }}" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="updateCPModalLabel-{{ $cp->id }}">Perbarui CP</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form action="{{ route('silabus.updateCP', [$mapel->id, $cp->id]) }}" method="POST" class="m-0">
                            @csrf
                            @method('POST')
                            <div class="modal-body">
                                <div class="form-group mb-3">
                                    <label for="nomor">CP</label>
                                    <input type="text" name="nomor" id="nomor" class="form-control" value="{{ $cp->nomor }}" required>
                                    @error('nomor')
                                    <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="form-group mb-3">
                                    <label for="nama">Topik</label>
                                    <input type="text" name="nama" id="nama" class="form-control" value="{{ $cp->nama }}" required>
                                    @error('nama')
                                    <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="form-group mb-3">
                                    <label for="keterangan">Keterangan</label>
                                    <input type="text" name="keterangan" id="keterangan" class="form-control" value="{{ $cp->keterangan }}" required>
                                    @error('keterangan')
                                    <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                <button type="submit" class="btn btn-primary">Simpan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            @endforeach
        </tbody>
    </table>

    <!-- Create CP Modal -->
    <div class="modal fade" id="createCPModal" tabindex="-1" aria-labelledby="createCPModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createCPModalLabel">Tambah CP Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('silabus.storeCP', $mapelId) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group mb-3">
                            <label for="nomor">CP</label>
                            <input type="text" name="nomor" id="nomor" class="form-control" required>
                            @error('nomor')
                            <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="form-group mb-3">
                            <label for="nama">Topik</label>
                            <input type="text" name="nama" id="nama" class="form-control" required>
                            @error('nama')
                            <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="form-group mb-3">
                            <label for="keterangan">Keterangan</label>
                            <input type="text" name="keterangan" id="keterangan" class="form-control" required>
                            @error('keterangan')
                            <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
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
            },
            columnDefs: [
                { width: "5%", targets: 0 }, // Kolom "TP"
                { width: "20%", targets: 1 }, // Kolom "Topik"
                { width: "50%", targets: 2 }, // Kolom "Keterangan"
                { width: "25%", targets: 3 }, // Kolom "Aksi"
            ],
            autoWidth: false,
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
@endpush