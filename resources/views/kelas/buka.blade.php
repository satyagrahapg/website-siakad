@extends('layout.layout')

@push('style')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.bootstrap5.css">
@endpush

@section('content')
    <div class="container-fluid mt-3">
        <div class="card mb-3 border-0 shadow-sm" style="background-color:#f2f2f2;">
            <div class="card-body" style="background-color: #37B7C3; border-radius: 8px">
                <h2 class="m-0" style="color: #EBF4F6">Kelas {{ $kelas->rombongan_belajar }}</h2>
            </div>
        </div>

        {{-- <!-- Form to filter based on Angkatan -->
        <form action="{{ route('kelas.buka', ['kelasId' => $kelas->id]) }}" method="GET">
            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="angkatan">Pilih Angkatan:</label>
                    <select name="angkatan" id="angkatan" class="form-control">
                        <option value="">-- Select Angkatan --</option>
                        @foreach ($angkatan as $year)
                            <option value="{{ $year }}" {{ request('angkatan') == $year ? 'selected' : '' }}>
                                {{ $year }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4 align-self-end">
                    <button type="submit" class="btn btn-primary">Filter</button>
                </div>
            </div>
        </form> --}}

        <!-- Add Student Modal Trigger -->
        <button class="btn btn-success mb-3" data-bs-toggle="modal"
            data-bs-target="#addStudentModal-{{ $kelas->id }}">Tambah</button>

        <!--Import & Export Student Modal Trigger -->
        <button class="btn btn-info mb-3" data-bs-toggle="modal"
            data-bs-target="#importStudentModal-{{ $kelas->id }}" style="width: 5rem">Impor</button>

        <a target="_blank" href="{{ route('kelas.export', ['kelasId' => $kelas->id]) }}" class="btn btn-secondary mb-3 px-3"
            style="width: 5rem">Ekspor</a>

        <!-- Auto Assign Student Modal Trigger -->
        <button class="btn btn-primary mb-3" data-bs-toggle="modal"
            data-bs-target="#autoAddStudentModal-{{ $kelas->id }}">Penempatan Otomatis</button>

        <table id="example" class="table table-striped" style="width:100%">
            <thead>
                <tr>
                    <th class="text-start" width="4%">No</th>
                    <th>Nama Siswa</th>
                    <th class="text-start">NISN</th>
                    <th class="text-start">Angkatan</th>
                    <th class="text-start">Jenis Kelamin</th>
                    <th class="text-start">Agama</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($daftar_siswa as $siswa)
                    <tr>
                        <td class="text-start">{{ $loop->iteration }}</td>
                        <td>{{ $siswa->nama }}</td>
                        {{-- debug purposes --}}
                        <td class="text-start">{{ $siswa->nisn }}</td>
                        <td class="text-start">{{ $siswa->angkatan }}</td>
                        <td class="text-start">{{ $siswa->jenis_kelamin }}</td>
                        <td class="text-start">{{ $siswa->agama }}</td>
                        <td>
                            <form
                                action="{{ route('kelas.siswa.delete', ['kelasId' => $kelas->id, 'siswaId' => $siswa->id]) }}"
                                method="POST" style="display:inline;"
                                onsubmit="return confirm('Are you sure you want to delete this student from this class?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger deleteAlert"><i class="fa-solid fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Add Student Modal -->
        <div class="modal fade" id="addStudentModal-{{ $kelas->id }}" tabindex="-1"
            aria-labelledby="addStudentModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ route('kelas.addStudent', $kelas->id) }}" method="POST">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title" id="addStudentModalLabel">Tambah Siswa ke
                                {{ $kelas->rombongan_belajar }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="id_siswa" class="form-label">Pilih Siswa</label>
                                <select name="id_siswa" class="form-select" required>
                                    <option value="" selected disabled hidden>Pilih Siswa</option>
                                    @foreach ($siswas as $siswa)
                                        <option value="{{ $siswa->id }}">{{ $siswa->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Tambah Siswa</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Auto-Add Student Modal -->
        <div class="modal fade" id="autoAddStudentModal-{{ $kelas->id }}" tabindex="-1"
            aria-labelledby="addStudentModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form
                        action="{{ route('kelas.autoAdd', ['kelasId' => $kelas->id, 'angkatan' => request('angkatan')]) }}"
                        method="POST" style="display:inline;">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title" id="addStudentModalLabel">Tempatkan Siswa Secara Otomatis</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="angkatan">Pilih Angkatan:</label>
                                <select name="angkatan" id="angkatan"
                                    class="form-select @error('angkatan') is-invalid @enderror">
                                    <option value="" selected disabled hidden>-- Select Angkatan --</option>
                                    @foreach ($angkatan as $year)
                                        <option value="{{ $year }}" @selected(old('angkatan') == $year)>
                                            {{ $year }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('angkatan')
                                    <p class="invalid-feedback">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="jumlahsiswa">Jumlah siswa dalam kelas :</label>
                                <input type="number" id="jumlahSiswa" name="jumlahSiswa"
                                    class="form-control @error('jumlahSiswa')
                                    is-invalid
                                @enderror"
                                    aria-describedby="passwordHelpBlock" value="{{ old('jumlahSiswa') ?? null }}">
                                @error('jumlahSiswa')
                                    <p class="invalid-feedback">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="mb-3 d-flex justify-content-between gap-2">
                                <div class="">
                                    <label for="jumlahsiswa">Jumlah siswa laki-laki:</label>
                                    @error('jumlahSiswaLaki')
                                        <label class="invalid-feedback">{{ $message }}</label>
                                    @enderror
                                    <div class="input-group">
                                        <button class="btn btn-outline-secondary" type="button"
                                            id="subtractMaleStudents"><i class="fa-solid fa-minus"></i></button>
                                        <input type="number" id="jumlahSiswaLaki" name="jumlahSiswaLaki"
                                            class="form-control @error('jumlahSiswaLaki') is-invalid @enderror"
                                            aria-describedby="passwordHelpBlock" readonly
                                            value="{{ old('jumlahSiswaLaki') ?? null }}">
                                        <button class="btn btn-outline-secondary" type="button" id="addMaleStudents"><i
                                                class="fa-solid fa-plus"></i></button>
                                    </div>
                                </div>
                                <div class="">
                                    <label for="jumlahsiswa">Jumlah siswa perempuan:</label>
                                    @error('jumlahSiswaPerempuan')
                                        <label class="form-label invalid-feedback">{{ $message }}</label>
                                    @enderror
                                    <div class="input-group">
                                        <button class="btn btn-outline-secondary" type="button"
                                            id="subtractFemaleStudents"><i class="fa-solid fa-minus"></i></button>
                                        <input type="number" id="jumlahSiswaPerempuan" name="jumlahSiswaPerempuan"
                                            class="form-control @error('jumlahSiswaPerempuan') is-invalid @enderror"
                                            aria-describedby="passwordHelpBlock" readonly
                                            value="{{ old('jumlahSiswaPerempuan') ?? null }}">
                                        <button class="btn btn-outline-secondary" type="button"
                                            id="addFemaleStudents"><i class="fa-solid fa-plus"></i></button>
                                    </div>

                                </div>
                            </div>

                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Tempatkan Siswa</button>
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
                        action="{{ route('kelas.importFromKelas', ['kelasId' => $kelas->id, 'angkatan' => request('angkatan')]) }}"
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
                                        <option value="{{ $semester->id }}" @selected(old('semester') == $year)>
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
    @if (session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    title: "Berhasil!",
                    text: "{{ session('success') }}",
                    icon: "success",
                    timer: 3000, // Waktu dalam milidetik (3000 = 3 detik)
                    showConfirmButton: false,
                    timeProgressBar: true
                });
            });
        </script>
    @endif

    <!-- error alert -->
    @if (session('error'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    title: "Gagal!",
                    text: "{{ session('error') }}",
                    icon: "error",
                    timer: 3000, // Waktu dalam milidetik (1500 = 1.5 detik)
                    showConfirmButton: false,
                    timeProgressBar: true
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
    @if ($errors->any())
        <script>
            const modal = document.getElementById('autoAddStudentModal-{{ $kelas->id }}');
            const myModal = new bootstrap.Modal(modal);
            myModal.show();
        </script>
    @endif
    <script>
        jumlahLaki = document.getElementById('jumlahSiswaLaki');
        jumlahPerempuan = document.getElementById('jumlahSiswaPerempuan');
        jumlahSiswa = document.getElementById('jumlahSiswa');
        addMaleStudents = document.getElementById('addMaleStudents');
        subtractMaleStudents = document.getElementById('subtractMaleStudents');
        addFemaleStudents = document.getElementById('addFemaleStudents');
        subtractFemaleStudents = document.getElementById('subtractFemaleStudents');

        // Event listener untuk menambah jumlah siswa laki-laki
        addMaleStudents.addEventListener('click', function() {
            jumlahLaki.value = parseInt(jumlahLaki.value) + 1;
            if (parseInt(jumlahLaki.value) > parseInt(jumlahSiswa.value)) {
                jumlahLaki.value = parseInt(jumlahSiswa.value);
            }
            jumlahPerempuan.value = parseInt(jumlahSiswa.value) - parseInt(jumlahLaki.value);
        });

        // Event listener untuk mengurangi jumlah siswa laki-laki
        subtractMaleStudents.addEventListener('click', function() {
            jumlahLaki.value = parseInt(jumlahLaki.value) - 1;
            if (parseInt(jumlahLaki.value) < 0) {
                jumlahLaki.value = 0;
            }
            jumlahPerempuan.value = parseInt(jumlahSiswa.value) - parseInt(jumlahLaki.value);
        });

        // Event listener untuk menambah jumlah siswa perempuan
        addFemaleStudents.addEventListener('click', function() {
            jumlahPerempuan.value = parseInt(jumlahPerempuan.value) + 1;
            if (parseInt(jumlahPerempuan.value) > parseInt(jumlahSiswa.value)) {
                jumlahPerempuan.value = parseInt(jumlahSiswa.value);
            }
            jumlahLaki.value = parseInt(jumlahSiswa.value) - parseInt(jumlahPerempuan.value);
        });

        // Event listener untuk mengurangi jumlah siswa perempuan
        subtractFemaleStudents.addEventListener('click', function() {
            jumlahPerempuan.value = parseInt(jumlahPerempuan.value) - 1;
            if (parseInt(jumlahPerempuan.value) < 0) {
                jumlahPerempuan.value = 0;
            }
            jumlahLaki.value = parseInt(jumlahSiswa.value) - parseInt(jumlahPerempuan.value);
        });

        // Event listener untuk menghitung jumlah siswa laki-laki dan perempuan ketika jumlah siswa diubah
        jumlahSiswa.addEventListener('change', function() {
            jumlahLaki.value = Math.floor(jumlahSiswa.value / 2);
            jumlahPerempuan.value = Math.ceil(jumlahSiswa.value / 2);
        });

        // Event listener untuk menghitung jumlah siswa perempuan ketika jumlah siswa laki-laki diubah
        jumlahLaki.addEventListener('change', function() {
            if (jumlahLaki.value > jumlahSiswa.value) {
                jumlahLaki.value = jumlahSiswa.value;
            }
            jumlahPerempuan.value = parseInt(jumlahSiswa.value) - parseInt(jumlahLaki.value);
        });

        // Event listener untuk menghitung jumlah siswa laki-laki ketika jumlah siswa perempuan diubah
        jumlahPerempuan.addEventListener('change', function() {
            if (jumlahPerempuan.value > jumlahSiswa.value) {
                jumlahPerempuan.value = jumlahSiswa.value;
            }
            jumlahLaki.value = parseInt(jumlahSiswa.value) - parseInt(jumlahPerempuan.value);
        });
    </script>
    <script>
        const selectSemester = document.getElementById('semester');
        semester.addEventListener('change', function() {
            console.log(semester.value)
            $.ajax({
                url: "{{ route('kelas.getKelas') }}",
                type: 'GET',
                data: {
                    semesterId: selectSemester.value
                },
                success: function(response) {
                    console.log(response);
                    const selectKelas = document.getElementById('kelas');
                    selectKelas.innerHTML = '<option value="">-- Select Kelas --</option>';
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
