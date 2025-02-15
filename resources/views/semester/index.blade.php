@extends('layout.layout')

@push('style')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.bootstrap5.css">
@endpush

@section('content')
    <div class="container-fluid mt-3">
        <div class="card mb-3 border-0 shadow-sm" style="background-color:#f2f2f2;">
            <div class="card-body" style="background-color: #37B7C3; border-radius: 8px">
                <h2 class="m-0" style="color: #EBF4F6">Semester</h2>
            </div>
        </div>

        <!-- Button trigger modal -->
        <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#createSemesterModal" style="width: 6rem">
            Tambah
        </button>

        <!-- toggle to enable "Edit" and "Delete" buttons -->
        <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" role="switch" id="flexSwitchCheckDefault" checked>  
            <label class="form-check-label" for="flexSwitchCheckDefault">Mode Edit</label>
        </div>
        

        <!-- Load the modal -->
        @include('modal.semester')

        <!-- Display existing semesters in a table -->
        <table id="example" class="table table-striped" style="width:100%">
            <thead>
                <tr>
                    <th class="text-start" style="width: 4%;">No</th>
                    <th class="text-start">Semester</th>
                    <th class="text-start">Tahun Ajaran</th>
                    <th class="text-start">Status</th>
                    <th class="text-start">Tanggal Dimulai</th>
                    <th class="text-start">Tanggal Berakhir</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($semesters as $semester)
                <tr>
                    <td class="text-start">{{ $loop->iteration }}</td>
                    <td class="text-start">{{ $semester->semester }}</td>
                    <td class="text-start">{{ $semester->tahun_ajaran }}</td>
                    <td class="text-start">{{ $semester->status == 1 ? 'Aktif' : 'Tidak Aktif' }}</td>
                    <td class="text-start">{{ $semester->start }}</td>
                    <td class="text-start">{{ $semester->end }}</td>
                    <td class="text-center">
                        <!-- Trigger Button for the Edit Modal -->
                        <button class="btn btn-warning controlled" data-bs-toggle="modal" data-bs-target="#editSemesterModal-{{ $semester->id }}"><i class="fa-solid fa-pen-to-square"></i></button>

                        <!-- Delete Button -->
                        <!-- <form action="{{ route('semesters.destroy', ['id' => $semester->id]) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this class?');"> -->
                        <form action="{{ route('semesters.destroy', ['id' => $semester->id]) }}" method="POST" style="display:inline;" >
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger deleteAlert controlled"><i class="fa-solid fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                <!-- Load the Modal -->
                @include('semester.semester-edit')
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