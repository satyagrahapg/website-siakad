@extends('layout.layout')

@push('style')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.bootstrap5.css">
@endpush

@section('content')
<div class="container-fluid mt-3">
    <div class="card mb-3 border-0 shadow-sm" style="background-color:#f2f2f2;">
        <div class="card-body" style="background-color: #37B7C3; border-radius: 8px">
            <h2 class="m-0" style="color: #EBF4F6">Projek Penguatan Profil Pelajar Pancasila (P5) {{ optional($kelasSemester)->rombongan_belajar ? '| '.$kelasSemester->rombongan_belajar: '' }}</h2>
        </div>
    </div>

    <!-- Student Dropdown -->
    <div class="form-group mb-3">
        <label for="siswa">Pilih Peserta Didik</label>
        <select id="siswa" class="form-control" style="width: 30%">
            <option value="" selected disabled hidden>Pilih Peserta Didik</option>
            @foreach ($siswaOptions as $siswa)
            <option value="{{ $siswa->id }}">{{ $siswa->nama }} - {{ $siswa->rombongan_belajar }}</option>
            @endforeach
        </select>
        
    </div>

    <button type="submit" id="submitButton" class="btn btn-primary mb-3">Simpan</button>

    <!-- P5BK Data Table -->
    <div id="p5bkTable" style="display:none;">
        <form id="p5bkFormData">
            @csrf
            <input type="hidden" name="semester_id" value="{{ $semesterId }}"> <!-- Ensure semester_id is included -->
            <input type="hidden" name="siswa_id" id="siswa_id">

            <!-- Table of Dimensions and Capaian -->
            <table id="example" class="table table-striped" style="width:100%">
                <thead>
                    <tr>
                        <th class="text-start" width="5%">No</th>
                        <th class="text-start">Dimensi Pengembangan</th>
                        <th class="text-start">Capaian</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach(['iman', 'kebhinekaan', 'mandiri', 'gotong-royong', 'kritis-kreatif'] as $index => $dimensi)
                    <tr>
                        <td class="text-start">{{ $loop->iteration }}</td>
                        <td class="text-start">{{ ucwords(str_replace('-', ' ', $dimensi)) }}</td>
                        <td class="text-start">
                            <select name="capaian[{{ $dimensi }}]" class="form-control capaian-select">
                                <option value="--">--</option>
                                <option value="MB">MB (Mulai Berkembang)</option>
                                <option value="SB">SB (Sedang Berkembang)</option>
                                <option value="BSH">BSH (Berkembang Sesuai Harapan)</option>
                                <option value="SAB">SAB (Sangat Berkembang)</option>
                            </select>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </form>
    </div>
</div>
@endsection

@push('script')
    
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
    document.addEventListener('DOMContentLoaded', function() {
        const siswaDropdown = document.getElementById('siswa');
        const p5bkTable = document.getElementById('p5bkTable');
        const siswaIdInput = document.getElementById('siswa_id');
        const form = document.getElementById('p5bkFormData');

        siswaDropdown.addEventListener('change', function() {
            const siswaId = this.value;
            if (!siswaId) {
                p5bkTable.style.display = 'none';
                return;
            }

            siswaIdInput.value = siswaId;

            // Fetch P5BK data via AJAX
            fetch(`{{ route('p5bk.fetch') }}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        semester_id: '{{ $semesterId }}',
                        siswa_id: siswaId
                    })
                })
                .then(response => response.json())
                .then(data => {
                    // Populate the form with fetched data
                    document.querySelectorAll('.capaian-select').forEach(select => {
                        const dimensi = select.name.replace('capaian[', '').replace(']', '');
                        select.value = data.find(item => item.dimensi === dimensi)?.capaian || '--';
                    });

                    p5bkTable.style.display = 'block';
                })
                .catch(error => console.error('Error:', error));
        });

        document.getElementById('submitButton').addEventListener('click', function(event) {
            event.preventDefault();

            // Save P5BK data via AJAX
            const formData = new FormData(form);

            fetch(`{{ route('p5bk.save', ['semesterId' => $semesterId]) }}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                Swal.fire({
                    title: "Berhasil!",
                    text: "P5 berhasil disimpan.",
                    icon: "success",
                    timer: 1500, // Waktu dalam milidetik (3000 = 3 detik)
                    showConfirmButton: false
                });
            });
        });
    });
</script>
@endpush