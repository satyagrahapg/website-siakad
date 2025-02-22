@extends('layout.layout')

@push('style')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.bootstrap5.css">
@endpush

@section('content')
<div class="container-fluid mt-3">
    <div class="card mb-3 border-0 shadow-sm" style="background-color:#f2f2f2;">
        <div class="card-body" style="background-color: #37B7C3; border-radius: 8px">
            <h2 class="m-0" style="color: #EBF4F6">Jam Pelajaran</h2>
        </div>
    </div>

    <!-- Button to open Create Mapel Modal -->
    <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#createJampelModal">Tambah Jam Pelajaran</button>

    {{-- Mata Pelajaran Table --}}
    <table id="example" class="table table-striped" style="width:100%">
        <thead>
            <tr>
                <th class="text-start">No</th>
                <th>Hari</th>
                <th class="text-start">Jam Pelajaran</th>
                <th>Jam Mulai</th>
                <th>Jam Selesai</th>
                <th>Durasi</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($jampels as $jampel)
            <tr>
                <td class="text-start">{{ $loop->iteration }}</td>
                <td>{{ $jampel->hari }}</td>
                <td class="text-start">{{ $jampel->nomor ? $jampel->nomor : "-" }}</td>
                <td>{{ substr($jampel->jam_mulai, 0, 5) }}</td>
                <td>{{ substr($jampel->jam_selesai, 0, 5) }}</td>
                @php
                    $jamMulai = new DateTime($jampel->jam_mulai);
                    $jamSelesai = new DateTime($jampel->jam_selesai);
                    $selisihDetik = $jamSelesai->getTimestamp() - $jamMulai->getTimestamp();
                    $totalMenit = $selisihDetik / 60;
                @endphp
                <td>{{ ($jampel->event ?? '').($jampel->event ? ' (' : '').$totalMenit.' menit'.($jampel->event ? ')' : '') }}</td>
                <td>
                    <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#ubahJampelModal-{{ $jampel->id }}"><i class="fa-solid fa-pen-to-square"></i></button>
                    
                    <form action="{{ route('jadwalmapel.delete-jampel', $jampel->id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger deleteAlert"><i class="fa-solid fa-trash"></i></button>
                    </form>

                    <div class="modal fade" id="ubahJampelModal-{{ $jampel->id }}" tabindex="-1" aria-labelledby="ubahJampelModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="ubahJampelModalLabel">Ubah Jam Pelajaran</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <form action="{{ route('jadwalmapel.update-jampel', $jampel->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label for="hari" class="form-label">Hari</label>
                                            <select name="hari" class="form-select" required>
                                                <option value="Senin" {{ $jampel->hari == "Senin" ? 'selected' : '' }}>Senin</option>
                                                <option value="Selasa" {{ $jampel->hari == "Selasa" ? 'selected' : '' }}>Selasa</option>
                                                <option value="Rabu" {{ $jampel->hari == "Rabu" ? 'selected' : '' }}>Rabu</option>
                                                <option value="Kamis" {{ $jampel->hari == "Kamis" ? 'selected' : '' }}>Kamis</option>
                                                <option value="Jumat" {{ $jampel->hari == "Jumat" ? 'selected' : '' }}>Jumat</option>
                                                <option value="Sabtu" {{ $jampel->hari == "Sabtu" ? 'selected' : '' }}>Sabtu</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label for="jam_mulai" class="form-label">Jam Mulai</label>
                                            <input type="time" class="form-control" name="jam_mulai" value="{{ old('jam_mulai', $jampel->jam_mulai) }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="jam_selesai" class="form-label">Jam Selesai</label>
                                            <input type="time" class="form-control" name="jam_selesai" value="{{ old('jam_selesai', $jampel->jam_selesai) }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="event" class="form-label">Kegiatan Khusus</label>
                                            <input type="text" class="form-control" name="event" value="{{ old('event', $jampel->event) }}">
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                        <button type="submit" class="btn btn-primary">Ubah Jam Pelajaran</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Modal for Create Jam Mapel -->
    <div class="modal fade" id="createJampelModal" tabindex="-1" aria-labelledby="createJampelModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createJampelModalLabel">Tambah Jam Pelajaran Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('jadwalmapel.store-jampel') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="hari" class="form-label">Hari</label>
                            <select name="hari" class="form-select" required>
                                <option value="Senin">Senin</option>
                                <option value="Selasa">Selasa</option>
                                <option value="Rabu">Rabu</option>
                                <option value="Kamis">Kamis</option>
                                <option value="Jumat">Jumat</option>
                                <option value="Sabtu">Sabtu</option>
                            </select>
                            
                        </div>
                        <div class="mb-3">
                            <label for="jam_mulai" class="form-label">Jam Mulai</label>
                            <input type="time" class="form-control" name="jam_mulai" required>
                        </div>
                        <div class="mb-3">
                            <label for="jam_selesai" class="form-label">Jam Selesai</label>
                            <input type="time" class="form-control" name="jam_selesai" required>
                        </div>
                        <div class="mb-3">
                            <label for="event" class="form-label">Kegiatan Khusus</label>
                            <input type="text" class="form-control" name="event">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary">Tambah Jam Pelajaran</button>
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
                {
                    targets: -1, // Menargetkan kolom terakhir (Aksi)
                    searchable: false // Menonaktifkan pencarian pada kolom ini
                }
            ]
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