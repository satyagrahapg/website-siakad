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
            <h2 class="m-0" style="color: #EBF4F6" style="color: #EBF4F6">Daftar Akun</h2>
        </div>
    </div>
    <div class="">
        {{-- toggle to enable "Edit" and "Delete" buttons --}} 
        <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" role="switch" id="flexSwitchCheckDefault" checked>  
            <label class="form-check-label" for="flexSwitchCheckDefault">Mode Edit</label>
        </div>
    </div>

    <table id="example" class="table table-striped" style="width:100%">
        <thead>
            <tr>
                <th class="text-start">ID</th>
                <th>Nama</th>
                <th>Nama Pengguna</th>
                <th>Email</th>
                <th>Hak Akses</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($accounts as $account)
            <tr>
                <td class="text-start">{{ $account->id }}</td>
                <td>{{ $account->name }}</td>
                <td>{{ $account->username }}</td>
                <td>{{ $account->email }}</td>
                <td>{{ count($account->getRoleNames()) > 0 ? $account->getRoleNames()->implode(', ') : "Tidak Ada" }}</td>
                <td>
                    {{-- <a href="{{ route('account.edit', $account->id) }}" class="btn btn-warning" style="width: 5rem">Ubah</a> --}}
                    <button class="btn btn-warning controlled" data-bs-toggle="modal" data-bs-target="#editAccountModal-{{ $account->id }}" ><i class="fa-solid fa-edit "></i></button>

                    <form action="{{ route('account.destroy', $account->id) }}" method="POST" style="display:inline;">
                        @csrf
                        <button type="submit" class="btn btn-danger deleteAlert controlled"><i class="fa-solid fa-trash"></i></button>
                    </form>
                </td>
            </tr>
            @include('account.update')
            @endforeach
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
        $(document).ready(function () {
            // Enable Edit and Delete buttons when toggle is checked
            $('#flexSwitchCheckDefault').on('change', function() {
                const isEditMode = this.checked;

                // Enable or disable all controlled buttons
                document.querySelectorAll('.controlled').forEach(button => {
                    button.disabled = !isEditMode;
                });
            });

            // Iterasi melalui setiap modal
            $('.modal').each(function () {
                const modal = $(this);
                const selectElements = modal.find('.role-multiple');

                selectElements.each(function () {
                    const selectElement = $(this); // Referensi elemen <select> saat ini

                    // Ambil opsi yang sudah terpilih saat inisialisasi
                    // const nonRemovableValues = selectElement.find('option:selected').map(function () {
                    //     return this.value;
                    // }).get();

                    // Inisialisasi Select2 dengan dropdownParent sesuai modal
                    selectElement.select2({
                        dropdownParent: modal,
                        width: '100%',
                        placeholder: "Pilih peran"
                    });

                    // Cegah penghapusan opsi yang sudah terpilih
                    // selectElement.on('select2:unselecting', function (e) {
                    //     const value = e.params.args.data.id; // ID dari opsi yang akan dihapus
                    //     if (nonRemovableValues.includes(value)) {
                    //         e.preventDefault(); // Cegah penghapusan
                    //     }
                    // });
                });
            });
        });
    </script>
@endpush