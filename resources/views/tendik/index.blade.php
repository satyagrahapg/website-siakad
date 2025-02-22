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
            <h2 class="m-0" style="color: #EBF4F6">Tenaga Kependidikan</h2>
        </div>
    </div>

    <!-- import modal -->
    <div class="modal fade" data-bs-backdrop="static" tabindex="-1" aria-hidden="true" id="excelModal">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        Impor Data Tenaga Kependidikan dari Excel
                    </h5>
                    <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('tendik.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="m-3">
                        <input type="file" name="file" class="form-control" accept=".xlsx" required placeholder="Pilih File">
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
    <button class="btn btn-primary mb-3 px-3" data-bs-toggle="modal" data-bs-target="#excelModal" style="width: 6rem">Impor</button>
    <a target="_blank" href="{{ route('tendik.export') }}" class="btn btn-secondary mb-3 px-3" style="width: 6rem">Ekspor</a>
    <button class="btn btn-success mb-3 px-2" data-bs-toggle="modal" data-bs-target="#createTendikModal" style="width: 6rem">Tambah</button>

    <!-- toggle to enable "Edit" and "Delete" buttons  -->
    <div class="form-check form-switch">
        <input class="form-check-input" type="checkbox" role="switch" id="flexSwitchCheckDefault" checked>  
        <label class="form-check-label" for="flexSwitchCheckDefault">Mode Edit</label>
    </div>

    <!-- Add Admin Button -->
    <!-- <button class="btn btn-success mt-3" data-bs-toggle="modal" data-bs-target="#createTendikModal">Tambah Data</button> -->

    <!-- Admin List -->
    <table id="example" class="table table-striped" style="width:100%">
        <thead>
            <tr>
                <th class="text-start">No</th>
                <th>Nama</th>
                <th class="text-start">NIP / Kode Pegawai</th>
                <th>Jenis Kelamin</th>
                <th>Jabatan</th>
                <th>Status</th>
                <th>Pendidikan</th>
                <th>Aksi</th>
                <th>Akun</th>
            </tr>
        </thead>
        <tbody>
            @foreach($tendik as $a)
            <tr>
                <td class="text-start">{{ $loop->iteration }}</td>
                <td>{{ $a->nama }}</td>
                <td class="text-start">{{ $a->nip }}</td>
                <td>{{ $a->jenis_kelamin }}</td>
                <td>{{ $a->jabatan}}</td>
                <td>{{ (strpos(old('status', $a->status), "TT")) ? $a->status : $a->status.' - '.$a->pangkat_golongan }}</td>
                <td>{{ $a->pendidikan }}</td>
                <td>
                    <div class="d-flex gap-2">
                    <!-- View Class Modal Trigger -->
                    <button class="btn btn-info" data-bs-toggle="modal" data-bs-target="#viewTendikModal-{{ $a->id }}"><i class="fa-solid fa-eye"></i></button>
                    <!-- Edit Class Modal Trigger -->
                    <button class="btn btn-warning controlled" data-bs-toggle="modal" data-bs-target="#editTendikModal-{{ $a->id }}"><i class="fa-solid fa-edit"></i></button>
                    @role('Super Admin')
                        <form action="{{ route('tendik.destroy', $a->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger deleteAlert controlled"><i class="fa-solid fa-trash"></i></button>
                        </form>
                    @endrole
                    </div>
                </td>
                <td>
                    @role('Super Admin')
                        @if(empty($a->id_user))
                            <!-- Button to open the generate user modal -->
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#generateUserModal-{{ $a->id }}">
                                <i class="fa-solid fa-plus"></i>
                            </button>
                        @else
                            {{-- <span> User ID: {{ $a->id_user }}</span> --}}
                            <a href="{{ route('account.index') }}">Lihat</a>
                        @endif
                    @endrole
                    @role('Admin')
                        @if(empty($a->id_user))
                            Belum Ada
                        @else
                            Sudah Ada
                        @endif
                    @endrole
                </td>
            </tr>
            @include('tendik.update')
            @include('tendik.view')
            @endforeach
        </tbody>
    </table>


    <!-- Include Modals -->
    @include('tendik._create_modal')
    @include('tendik._generate_user_modal')
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
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const statusSelects = document.querySelectorAll('#status-option');
            const pangkatGolonganSelects = document.querySelectorAll('#golongan-option');
            const golonganTitles = document.querySelectorAll('#golongan-title');
            const golonganHiddenElements = document.querySelectorAll('#golongan-hidden'); // Mengambil semua elemen dengan id yang sama

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
                const golonganTitle = golonganTitles[index];
                const golonganHiddenElement = golonganHiddenElements[index]; // Ambil elemen yang sesuai berdasarkan indeks

                statusSelect.addEventListener('change', function () {
                    const selectedStatus = this.value;

                    if (selectedStatus === "PNS" || selectedStatus === "PPPK") {
                        // Show and enable select and title
                        pangkatGolonganSelect.hidden = false;
                        pangkatGolonganSelect.disabled = false;
                        golonganTitle.hidden = false;

                        // Clear existing options
                        pangkatGolonganSelect.innerHTML = "";

                        // Populate options based on selected status
                        const options = selectedStatus === "PNS" ? optionsPNS : optionsPPPK;

                        options.forEach(option => {
                            const opt = document.createElement('option');
                            opt.value = option.value;
                            opt.textContent = option.text;
                            pangkatGolonganSelect.appendChild(opt);
                        });

                        // Disable golongan-hidden element
                        if (golonganHiddenElement) {
                            golonganHiddenElement.disabled = true;
                            golonganHiddenElement.value = null; // Reset value to null
                        }
                    } else {
                        // Hide and disable select and title
                        pangkatGolonganSelect.hidden = true;
                        pangkatGolonganSelect.disabled = true;
                        golonganTitle.hidden = true;

                        // Enable golongan-hidden element
                        if (golonganHiddenElement) {
                            golonganHiddenElement.disabled = false;
                        }
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