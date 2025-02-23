@extends('layout.layout')

@push('style')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.bootstrap5.css">
@endpush

@section('content')
<div class="container-fluid mt-3">
    <div class="card mb-3 border-0 shadow-sm" style="background-color:#f2f2f2;">
        <div class="card-body" style="background-color: #37B7C3; border-radius: 8px">
            <h2 class="m-0" style="color: #EBF4F6">Kelas & Ekstrakurikuler</h2>
        </div>
    </div>

    <!-- Filter Form -->
    <form action="{{ route('kelas.index') }}" method="GET" class="mb-4">
        <div class="row">
            <!-- Semester Filter -->
            <div class="col-md-4">
                <label for="semester_id">Semester:</label>
                <select name="semester_id" id="semester_id" class="form-control">
                    <option value="" selected disabled hidden>Pilih Semester</option>
                    @foreach($semesters as $semester)
                    <option value="{{ $semester->id }}" {{ request('semester_id') == $semester->id ? 'selected' : '' }}>
                        {{ $semester->semester }} | {{ $semester->tahun_ajaran }} {{ $semester->status == 1 ? "(Aktif)" : "" }}
                    </option>
                    @endforeach
                </select>
            </div>

            <!-- Class Filter -->
            <div class="col-md-4">
                <label for="kelas">Kelas:</label>
                <select name="kelas" id="kelas" class="form-control">
                    <option value="" selected disabled hidden>Pilih Kelas</option>
                    @foreach($listKelas as $class)
                    <option value="{{ $class->kelas }}" {{ request('kelas') == $class->kelas ? 'selected' : '' }}>
                        {{ $class->kelas }}
                    </option>
                    @endforeach
                </select>
            </div>

            <!-- Filter Button -->
            <div class="col-md-4 align-self-end">
                <button type="submit" class="btn btn-primary">Filter</button>
            </div>
        </div>
    </form>

    <!-- @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
    @endif -->

    <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#createKelasModal">Tambah Kelas</button>
    <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#createEkstrakulikulerModal">Tambah Ekstrakurikuler</button>

    <!-- toggle to enable "Edit" and "Delete" buttons  -->
    <div class="form-check form-switch">
        <input class="form-check-input" type="checkbox" role="switch" id="flexSwitchCheckDefault" checked>  
        <label class="form-check-label" for="flexSwitchCheckDefault">Mode Edit</label>
    </div>

    <table id="example" class="table table-striped" style="width:100%">
        <thead>
            <tr>
                <th class="text-start">No</th>
                <th class="text-start">Kelas</th>
                <th>Rombongan Belajar</th>
                <th>Wali Kelas / Pendamping</th>
                <th>Semester</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($kelas as $k)
            <tr>
                <td class="text-start">{{$loop->iteration}}</td>
                <td class="text-start">{{ $k->kelas }}</td>
                <td>{{ $k->rombongan_belajar }}</td>
                <td>{{ $k->guru->nama ?? 'N/A' }}</td>
                <td>{{ $k->semester->semester . " | " . $k->semester->tahun_ajaran }}</td>
                <td>
                    <!-- View Students Button -->
                    <a href="{{ route('kelas.buka', $k->id) }}" class="btn btn-info"><i class="fa-solid fa-door-open"></i></a>

                    <!-- Edit Class Modal Trigger -->
                    <button class="btn btn-warning controlled" data-bs-toggle="modal" data-bs-target="#editKelasModal-{{ $k->id }}"><i class="fa-solid fa-pen-to-square"></i></button>

                    <!-- Edit Class Modal -->
                    @if ($k->kelas != 'Ekskul')
                    <div class="modal fade" id="editKelasModal-{{ $k->id }}" tabindex="-1" aria-labelledby="editKelasModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form action="{{ route('kelas.update', $k->id) }}" method="POST">
                                    @csrf
                                    @method('POST') <!-- Use PUT for updates as per REST conventions -->
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="editKelasModalLabel">Ubah Kelas - {{ $k->rombongan_belajar }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label for="kelas" class="form-label">Kelas</label>
                                            <input type="text" name="kelas" class="form-control" value="{{ $k->kelas }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="rombongan_belajar" class="form-label">Rombongan Belajar</label>
                                            <input type="text" name="rombongan_belajar" class="form-control" value="{{ $k->rombongan_belajar }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="id_guru" class="form-label">Wali Kelas</label>
                                            <select name="id_guru" class="form-select" required>
                                                <option value="" selected disabled hidden>Pilih Wali Kelas</option>
                                                @foreach($walikelas as $guru)
                                                <option value="{{ $guru->id }}" {{ $guru->id == $k->id_guru ? 'selected' : '' }}>
                                                    {{ $guru->nama }}
                                                </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label for="id_semester" class="form-label">Semester</label>
                                            <select name="id_semester" class="form-select" required>
                                                <option value="" selected disabled hidden>Pilih Semester</option>
                                                @foreach($semesters as $semester)
                                                <option value="{{ $semester->id }}" {{ $semester->id == $k->id_semester ? 'selected' : '' }}>
                                                    {{ $semester->semester . " | " . $semester->tahun_ajaran . ($semester->status == 1 ? " | Aktif" : "") }}
                                                </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit" class="btn btn-primary">Simpan</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @else
                    <div class="modal fade" id="editKelasModal-{{ $k->id }}" tabindex="-1" aria-labelledby="editKelasModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form action="{{ route('kelas.update', $k->id) }}" method="POST">
                                    @csrf
                                    @method('POST') <!-- Use PUT for updates as per REST conventions -->
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="editEkstraModalLabel">Ubah Ekstrakurikuler - {{ $k->rombongan_belajar }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label for="kelas" class="form-label">Kelas</label>
                                            <input type="text" name="kelas" class="form-control" value="{{ $k->kelas }}" readonly>
                                        </div>
                                        <div class="mb-3">
                                            <label for="rombongan_belajar" class="form-label">Nama Ekstrakurikuler</label>
                                            <input type="text" name="rombongan_belajar" class="form-control" value="{{ $k->rombongan_belajar }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="id_guru" class="form-label">Pendamping</label>
                                            <select name="id_guru" class="form-select" required>
                                                <option value="" selected disabled hidden>Pilih Pelatih</option>
                                                @foreach($gurus as $guru)
                                                <option value="{{ $guru->id }}" {{ $guru->id == $k->id_guru ? 'selected' : '' }}>
                                                    {{ $guru->nama }}
                                                </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label for="id_semester" class="form-label">Semester</label>
                                            <select name="id_semester" class="form-select" required>
                                                <option value="" selected disabled hidden>Pilih Semester</option>
                                                @foreach($semesters as $semester)
                                                <option value="{{ $semester->id }}" {{ $semester->id == $k->id_semester ? 'selected' : '' }}>
                                                    {{ $semester->semester . " | " . $semester->tahun_ajaran . ($semester->status == 1 ? " | Aktif" : "") }}
                                                </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit" class="btn btn-primary">Simpan</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endif
                    <!-- Write another modal for non Ekstra -->

                    <!-- Delete Class Button -->
                    <form action="{{ route('kelas.hapus', ['kelasId' => $k->id]) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this class?');">
                        @csrf
                        <button type="submit" class="btn btn-danger deleteAlert controlled"><i class="fa-solid fa-trash"></i></button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Create Kelas Modal -->
    <div class="modal fade" id="createKelasModal" tabindex="-1" aria-labelledby="createKelasModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ route('kelas.store') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="createKelasModalLabel">Tambah Kelas Baru</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="kelas" class="form-label">Kelas</label>
                            <select name="kelas" class="form-select" required>
                                <option value="7">7</option>
                                <option value="8">8</option>
                                <option value="9">9</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="rombongan_belajar" class="form-label">Rombongan Belajar</label>
                            <input type="text" name="rombongan_belajar" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="id_semester" class="form-label">Semester</label>
                            <select name="id_semester" id="semester_kelas" class="form-select" required>
                                <option value="" selected disabled hidden>Pilih Semester</option>
                                @foreach($semesters as $semester)
                                    <option value="{{ $semester->id }}">{{ $semester->semester . " | " . $semester->tahun_ajaran . ($semester->status == 1 ? " | Aktif" : "") }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="id_guru" class="form-label">Wali Kelas</label>
                            <select name="id_guru" id="guru_walikelas" class="form-select" required disabled>
                                <option value="" selected disabled hidden>Pilih Wali Kelas</option>
                                {{-- @foreach($walikelas as $guru)
                                    <option value="{{ $guru->id }}">{{ $guru->nama }}</option>
                                @endforeach --}}
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal untuk membuat Ekstrakulikuler -->
    <!-- Create Kelas Modal -->
    <div class="modal fade" id="createEkstrakulikulerModal" tabindex="-1" aria-labelledby="createEkstrakulikulerModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('kelas.storeEkskul') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="createEkstrakulikulerModalLabel">Tambah Ekstrakulikuler Baru</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="rombongan_belajar" class="form-label">Nama Ekstrakulikuler</label>
                            <input type="text" name="rombongan_belajar" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="id_guru" class="form-label">Pendamping</label>
                            <select name="id_guru" class="form-select" required>
                                <option value="" selected disabled hidden>Pilih Pendamping Ekstrakulikuler</option>
                                @foreach($gurus as $guru)
                                <option value="{{ $guru->id }}">{{ $guru->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="id_semester" class="form-label">Semester</label>
                            <select name="id_semester" class="form-select" required>
                                <option value="" selected disabled hidden>Pilih Semester</option>
                                @foreach($semesters as $semester)
                                <option value="{{ $semester->id }}">{{ $semester->semester . " | " . $semester->tahun_ajaran . ($semester->status == 1 ? " | Aktif" : "") }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
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
            timer: 1500, // Waktu dalam milidetik (1500 = 1.5 detik)
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

<!-- script datatable -->
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

<!-- konfirmasi sweetalert -->
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

<script>
    function loadWalikelas() {
        const semesterId = $('#semester_kelas').val();

        $('#guru_walikelas').empty().append('<option value="" selected hidden disabled>Pilih Wali Kelas</option>').prop('disabled', true);
        
        if (semesterId) {
            $.ajax({
                url: '{{ route("kelas.getWaliKelas") }}',
                type: 'GET',
                data: {
                    semesterId: semesterId,
                },
                success: function (data) {
                    $('#guru_walikelas').prop('disabled', false);
                    if (data.length > 0) {
                        data.forEach(walikelas => {
                            $('#guru_walikelas').append(`<option value="${walikelas.id}">${walikelas.nama}</option>`);
                        });
                    } else {
                        $('#guru_walikelas').append('<option value="" disabled>Tidak ada data</option>');
                    }
                }
            });
        }
    }

    $('#semester_kelas').on('change', function () {
        loadWalikelas();
    });
</script>
@endpush