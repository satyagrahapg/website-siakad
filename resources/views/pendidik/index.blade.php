@extends('layout.layout')

@push('style')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.bootstrap5.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">  
@endpush

@section('content')
<div class="container-fluid mt-3">
    <div class="card mb-3 border-0 shadow-sm" style="background-color:#f2f2f2;">
        <div class="card-body" style="background-color: #37B7C3; border-radius: 8px">
            <h2 class="m-0" style="color: #EBF4F6">Pendidik</h2>
        </div>
    </div>

    <!-- import modal -->
    <div class="modal fade" data-bs-backdrop="static" tabindex="-1" aria-hidden="true" id="excelModal">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        Impor Data Pendidik dari Excel 
                    </h5>
                    <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('pendidik.import') }}" method="POST" enctype="multipart/form-data">
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

    <!-- import button -->
    <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#excelModal" style="width: 6rem">Impor</button>
    <a target="_blank" href="{{ route('pendidik.export') }}" class="btn btn-secondary mb-3 px-3" style="width: 6rem">Ekspor</a>
    <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#createGuruModal" style="width: 6rem">Tambah</button>
    
    {{-- toggle to enable "Edit" and "Delete" buttons --}} 
    <div class="form-check form-switch">
        <input class="form-check-input" type="checkbox" role="switch" id="flexSwitchCheckDefault" checked>  
        <label class="form-check-label" for="flexSwitchCheckDefault">Mode Edit</label>
    </div>

    <!-- Guru List -->
    <table id="example" class="table table-striped" style="width:100%">
        <thead>
            <tr>
                <th class="text-start">No</th>
                <th>Nama</th>
                <th class="text-start">NIP / Kode Pegawai</th>
                <th>Jenis Kelamin</th>
                <th>Jabatan</th>
                <th>Status - Pangkat</th>
                <th>Pendidikan</th>
                <th>Aksi</th>
                <th>Akun</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pendidiks as $guru)
                <tr>
                    <td class="text-start">{{ $loop->iteration }}</td>
                    <td>{{ $guru->nama }}</td>
                    <td class="text-start">{{ $guru->nip }}</td>
                    <td>{{ $guru->jenis_kelamin ?? ' - ' }}</td>
                    <td>{{ $guru->jabatan ?? ' - ' }}</td>
                    <td>{{ $guru->pangkat_golongan ? $guru->status.' - '.$guru->pangkat_golongan : ($guru->status ?? ' - ') }}</td>
                    <td>{{ $guru->pendidikan ?? ' - ' }}</td>
                    <td >
                        <div class="d-flex gap-2">
                        <!-- View Class Modal Trigger -->   
                        <button class="btn btn-info" data-bs-toggle="modal" data-bs-target="#viewGuruModal-{{ $guru->id }}"><i class="fa-solid fa-eye"></i></button>
                        <!-- Edit Class Modal Trigger -->   
                        <button class="btn btn-warning controlled" data-bs-toggle="modal" data-bs-target="#editGuruModal-{{ $guru->id }}"><i class="fa-solid fa-edit"></i></button>
                        @role('Super Admin')
                            <form action="{{ route('pendidik.destroy', $guru->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger deleteAlert controlled"><i class="fa-solid fa-trash"></i></button>
                            </form>
                        @endrole
                        </div>
                    </td>
                    <td>
                        @if(empty($guru->id_user))
                        <!-- Button to open the generate user modal -->
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#generateUserModal-{{ $guru->id }}" style="min-width: 42px;"><i class="fa-solid fa-plus"></i></button>
                        @include('pendidik.generate')
                        @else
                            @role("Super Admin")
                                {{-- <span>User ID: {{ $guru->id_user }}</span> --}}
                                <a href="{{ route('account.index') }}">Lihat</a>
                            @else
                                {{-- <span>Sudah Ada</span> --}}
                                <button class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#editRoleModal-{{ $guru->id }}" style="min-width: 42px;"><i class="fa-solid fa-edit"></i></button>
                                @include('pendidik.role')
                            @endrole
                        @endif
                    </td>
                </tr>
                @include('pendidik.update')
                @include('pendidik.view')
            @endforeach
            @include('pendidik.create')
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
    @if ($errors->any())
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                let errorMessages = '';
                @foreach ($errors->all() as $error)
                    errorMessages += '{{ $error }} ';
                @endforeach
                
                Swal.fire({
                    title: "Error!",
                    html: errorMessages, // Menggunakan properti html untuk menampilkan list
                    icon: "error",
                    timer: 3000,
                    showConfirmButton: false
                });
            });
        </script>
    @endif
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script> --}}
    <script src="https://cdn.datatables.net/2.1.8/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.1.8/js/dataTables.bootstrap5.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>  
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
        document.addEventListener('DOMContentLoaded', () => {
            const statusSelects = document.querySelectorAll('#status-option');
            const pangkatGolonganSelects = document.querySelectorAll('#golongan-option');

            const optionsPNS = [
                { value: "III/a", text: "III/a" },
                { value: "III/b", text: "III/b" },
                { value: "III/c", text: "III/c" },
                { value: "III/d", text: "III/d" },
                { value: "IV/a", text: "IV/a" },
                { value: "IV/b", text: "IV/b" },
                { value: "IV/c", text: "IV/c" },
                { value: "IV/d", text: "IV/d" },
                { value: "IV/e", text: "IV/e" },
            ];

            const optionsPPPK = [
                { value: "IX", text: "IX" },
                { value: "X", text: "X" },
                { value: "XI", text: "XI" },
                { value: "XII", text: "XII" },
                { value: "XIII", text: "XIII" },
                { value: "XIV", text: "XIV" },
                { value: "XV", text: "XV" },
                { value: "XVI", text: "XVI" },
                { value: "XVII", text: "XVII" },
            ];

            statusSelects.forEach((statusSelect, index) => {
                const pangkatGolonganSelect = pangkatGolonganSelects[index];

                statusSelect.addEventListener('change', function () {
                    const selectedStatus = this.value;

                    // Clear existing options
                    pangkatGolonganSelect.innerHTML = "";
                    const defaultOption = document.createElement('option');
                    defaultOption.value = "";
                    defaultOption.textContent = "Pilih Pangkat Golongan";
                    defaultOption.selected = true;
                    defaultOption.disabled = true;
                    defaultOption.hidden = true;
                    pangkatGolonganSelect.appendChild(defaultOption);

                    // Populate options based on selected status
                    const options = selectedStatus === "PNS" ? optionsPNS : optionsPPPK;

                    options.forEach(option => {
                        const opt = document.createElement('option');
                        opt.value = option.value;
                        opt.textContent = option.text;
                        pangkatGolonganSelect.appendChild(opt);
                    });
                });
            });
        });
    </script>
    <script>
        $(document).ready(function () {
            // Iterasi melalui setiap modal
            $('.modal').each(function () {
                const modal = $(this);
                const selectElements = modal.find('.role-multiple');

                selectElements.each(function () {
                    const selectElement = $(this); // Referensi elemen <select> saat ini

                    // Ambil opsi yang sudah terpilih saat inisialisasi
                    const nonRemovableValues = selectElement.find('option:selected').map(function () {
                        return this.value;
                    }).get();

                    // Inisialisasi Select2 dengan dropdownParent sesuai modal
                    selectElement.select2({
                        dropdownParent: modal,
                        width: '100%',
                        placeholder: "Pilih peran"
                    });

                    // Cegah penghapusan opsi yang sudah terpilih
                    selectElement.on('select2:unselecting', function (e) {
                        const selectedText = e.params.args.data.text; // Ambil teks dari opsi yang akan dihapus
                        if (selectedText === 'Guru') {
                            e.preventDefault(); // Cegah penghapusan jika teks adalah "Guru"
                        }
                    });
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