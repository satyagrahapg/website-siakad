@extends('layout.layout')

@push('style')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.bootstrap5.css">
@endpush

@section('content')
<div class="container-fluid mt-3">
    <div class="card mb-3 border-0 shadow-sm" style="background-color:#f2f2f2;">
        <div class="card-body" style="background-color: #37B7C3; border-radius: 8px">
            @foreach ($penilaianEkskuls as $p )
                <h2 class="m-0" style="color: #EBF4F6">Penilaian | {{$p->kelas->rombongan_belajar}}</h2>
                @break
            @endforeach
        </div>
    </div>

    <!-- modal Informasi -->
    <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#infoPenilaianModal">
        <i class="fa-solid fa-circle-info"></i> Informasi
    </button>

    <!-- Modal -->
    <div class="modal fade" id="infoPenilaianModal" tabindex="-1" aria-labelledby="infoModalTP" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Informasi</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>NISN : Nomor Induk Siswa Nasional</p>
                </div>
            </div>
        </div>
    </div>

    <form action="{{ route('penilaian.ekskul.update.all', ['kelasId' => $kelasId, $mapelId]) }}" method="POST">
        <button type="submit" class="btn btn-primary mb-3">Perbarui Penilaian</button>
        @csrf
        @method('POST')

        <table id="example" class="table table-striped" style="width:100%">
            <thead>
                <tr>
                    <th class="text-start" width="5%">No</th>
                    <th>Nama</th>
                    <th class="text-start">NISN</th>
                    <th>Nilai</th>
                </tr>
            </thead>
            <tbody>
                @foreach($penilaianEkskuls as $penilaianEkskul)
                <tr>
                    <td class="text-start">{{$loop->iteration}}</td>
                    <td>{{ $penilaianEkskul->siswa->nama }}</td>
                    <td class="text-start">{{ $penilaianEkskul->siswa->nisn }}</td>
                    <td>
                        <input type="number" name="nilai[{{ $penilaianEkskul->id }}][nilai]" class="form-control" value="{{ old("nilai.{$penilaianEkskul->id}.nilai", $penilaianEkskul->nilai) }}" min="0" max="100">
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </form>

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
@endpush