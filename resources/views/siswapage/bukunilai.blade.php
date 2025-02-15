@extends('layout.layout')

@push('style')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.bootstrap5.css">
@endpush

@section ('content')
<div class="container-fluid mt-3">
    <div class="card mb-3 border-0 shadow-sm" style="background-color:#f2f2f2;">
        <div class="card-body" style="background-color: #37B7C3; border-radius: 8px">
            <h2 class="m-0" style="color: #EBF4F6">Peserta Didik</h2>
        </div>
    </div>
    <!-- Form to select a Mapel -->
    <select class="form-select mb-3" id="mapelSelect">
        <option value="" disabled selected hidden>Pilih Mata Pelajaran</option>
        @forelse ($mapels as $mapel)
            <option value="{{ $mapel->id }}">{{ $mapel->nama }}</option>
        @empty
            <option value="" disabled>Tidak Ada Mata Pelajaran</option>
        @endforelse
    </select>
    
    <!-- Display Average Scores -->
    <div class="row justify-content-evenly">
        <div class="col">
            <div class="card mb-3 border-0 shadow-sm" style="background-color:#f2f2f2;">
                <div class="card-body" style="background-color: #37B7C3; border-radius: 8px">
                    <h3 class="text-center m-0" style="color: #EBF4F6">Rata-rata Tugas : <span id="nilaiAkhirTugas">0</span></h3>
                </div>
            </div>
            <!-- <h3>Average Tugas: <span id="nilaiAkhirTugas"></span></h3> -->
        </div>
        <div class="col">
            <div class="card mb-3 border-0 shadow-sm" style="background-color:#f2f2f2;">
                <div class="card-body" style="background-color: #37B7C3; border-radius: 8px">
                    <h3 class="text-center m-0" style="color: #EBF4F6">Rata-rata UH : <span id="nilaiAkhirUH">0</span></h3>
                </div>
            </div>
            <!-- <h3>Average UH: <span id="nilaiAkhirUH"></span></h3> -->
        </div>
    </div>
    
    <!-- Table to display Penilaian details -->
    <table id="example" class="penilaian-table table table-striped" style="width:100%">
        <thead>
            <tr>
                <th>No</th>
                <th>Judul</th>
                <th>Tanggal</th>
                <th>Tipe</th>
                <th>TP</th>
                <th>Nilai</th>
            </tr>
        </thead>
        <tbody>
            <!-- This will be populated dynamically by AJAX -->
        </tbody>
    </table>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.js"></script>
<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script> -->
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
<!-- Add AJAX script to fetch data dynamically -->
<!-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> -->
<script>
    $(document).ready(function() {
        $('#mapelSelect').change(function() {
            var idMapel = $(this).val();

            if (idMapel) {
                console.log(idMapel);
                $.ajax({
                    url: '{{ route('fetchBukuNilai') }}',  // Use the correct route for the AJAX call
                    method: 'GET',
                    data: { idMapel: idMapel },
                    success: function(response) {
                        // Update the scores and table data
                        $('#nilaiAkhirTugas').text(response.nilaiAkhirTugas);
                        $('#nilaiAkhirUH').text(response.nilaiAkhirUH);

                        // Populate the table with penilaian data
                        var penilaianTableBody = $('.penilaian-table tbody');
                        penilaianTableBody.empty(); // Clear the existing table rows

                        // Menambahkan variabel counter untuk menghitung iterasi
                        var counter = 1;

                        response.penilaians.forEach(function(item) {
                            penilaianTableBody.append(`
                                <tr>
                                    <td>${counter}</td>
                                    <td>${item.judul}</td>
                                    <td>${item.tanggal}</td>
                                    <td>${item.tipe}</td>
                                    <td>${item.nomor_tp}</td>
                                    <td>${item.nilai}</td>
                                </tr>
                            `);
                            counter++; // Increment the counter for the next row
                        });
                    }
                });
            }
        });
    });
</script>
@endsection