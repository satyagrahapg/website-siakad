@extends('layout.layout')

@push('style')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.bootstrap5.css">
    <link rel="stylesheet" href="{{ asset('style/css/jquery.schedule.min.css') }}">
    <style>
        .jqs-period-time,
        .jqs-grid-hour,
        .jqs-options-time,
        .jqs-options-title-container,
        .jqs-options-duplicate,
        .jqs-period-remove,
        .jqs-period-duplicate,
        .jqs-day-remove,
        .jqs-day-duplicate,
        .jqs-options {
            display: none !important;
        }

        .jqs-grid {
            left: 20px;
        }

        .jqs-period-title {
            font-size: 14px;
            padding-top: 4px;
            overflow: visible;
            line-height: 15px;
            letter-spacing: normal;
        }

        .jqs-grid-day {
            font-size: 18px;
            padding: 0;
        }

        .jqs-option-remove {
            margin: 35px 5px 5px;
        }

        .form-group .jqs-period-container {
            position: unset;
            min-height: 5rem;
        }

        .form-group .jqs-period-container .jqs-period-title {
            font-size: 15px;
        }
    </style>
@endpush

@section('content')
<div class="container-fluid mt-3">
    <div class="card mb-3 border-0 shadow-sm" style="background-color:#f2f2f2;">
        <div class="card-body" style="background-color: #37B7C3; border-radius: 8px">
            <h2 class="m-0" style="color: #EBF4F6">Jadwal Mata Pelajaran</h2>
        </div>
    </div>
    @role('Admin|Super Admin')
    <div class="row mb-4">
        <div class="col-3">
            <label for="semester">Semester:</label>
            <select id="semester" name="semester" class="form-select">
                <option value="" disabled hidden selected>Pilih Semester</option>
                    @foreach($semesters as $semester)
                    <option value="{{ $semester->id }}" {{ request('semester_id') == $semester->id ? 'selected' : '' }}>
                        {{ $semester->semester }} | {{ $semester->tahun_ajaran }} {{ $semester->status == 1 ? "(Aktif)" : "" }}
                    </option>
                    @endforeach
            </select>
        </div>
        <div class="col-2">
            <label for="kelas">Kelas:</label>
            <select id="kelas" name="kelas" class="form-select" disabled>
                <option value="">Pilih Kelas</option>
            </select>
        </div>
        <div class="col-2">
            <label for="rombel">Rombongan Belajar:</label>
            <select id="rombel" name="rombel" class="form-select" disabled>
                <option value="">Pilih Rombel</option>
            </select>
        </div>
        <div class="col">
            <button class="btn btn-primary" id="btnShowCalendar" style="margin-top: 24px;">Lihat Kalender</button>
            <a href="{{ route('jadwalmapel.index-jampel') }}" class="btn btn-warning text-white" style="margin-top: 24px; margin-left:16px;">Buka Jam Pelajaran</a>
        </div>
    </div>
    <div class="row mb-4">
        <div class="col-6">
            <label for="ma_pel">Mata Pelajaran:</label>
            <select id="ma_pel" name="ma_pel" class="form-select" disabled>
                <option value="">Pilih Mata Pelajaran</option>
            </select>
        </div>
        <div class="col-3">
            <label for="jampel">Jam Pelajaran:</label>
            <select id="jampel" name="jampel" class="form-select" disabled>
                <option value="">Pilih Jam Pelajaran</option>
            </select>
        </div>
        <div class="col">
            <button class="btn btn-success" id="btnTambahJadwal" style="margin-top: 24px;" disabled>Tambah Jadwal</button>
        </div>
    </div>
    @endrole

    <div class="row">
        <div class="col">
            <h3 class="text-center mt-4 mb-2">Jadwal Jam Mata Pelajaran</h3>
            <div id="schedule" style="overflow-y: hidden; overflow-x: hidden; padding: 40px 20px 20px 20px;"></div>
        </div>
    </div>

    @role('Admin|Super Admin')
    <div class="modal" id="editJampelKalenderModal" tabindex="-1" aria-labelledby="editJampelKalenderModal-" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <input type="hidden" id="jpmk-id">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Hapus Jadwal Jam Mata Pelajaran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <h6>Hari <span id="jpmk-hari">Hari</span></h6>
                    <div class="form-group mb-3" id="showTinyCalendar"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="button" id="btnHapusJPMK" class="btn btn-danger" data-bs-dismiss="modal">Hapus</button>
                </div>
            </div>
        </div>
    </div>

    <button class="btn btn-warning" id="modalButton" data-bs-toggle="modal" data-bs-target="#editJampelKalenderModal" style="width: 5rem; display: none;">Ubah</button>
    @endrole
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
{{-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script> --}}
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
<script src="{{ asset('style/js/jquery.schedule.min.js') }}"></script>
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
@role('Admin|Super Admin')
<script>
    $(document).ready(function () {
        $('#semester').on('change', function () {
            const semesterId = $(this).val();

            $('#kelas').empty().append('<option value="" selected hidden disabled>Pilih Kelas</option>').prop('disabled', true);
            $('#rombel').empty().append('<option value="" selected hidden disabled>Pilih Rombel</option>').prop('disabled', true);
            $('#ma_pel').empty().append('<option value="" selected hidden disabled>Pilih Mata Pelajaran</option>').prop('disabled', true);
            $('#jampel').empty().append('<option value="" selected hidden disabled>Pilih Jam Pelajaran</option>').prop('disabled', true);
            $('#btnTambahJadwal').prop('disabled', true);

            if (semesterId) {
                $.ajax({
                    url: '{{ route("jadwalmapel.ajaxHandler") }}',
                    type: 'GET',
                    data: {
                        action: 'getKelas',
                        semesterId: semesterId,
                    },
                    success: function (data) {
                        $('#kelas').prop('disabled', false);
                        if (data.length > 0) {
                            data.forEach(kelas => {
                                $('#kelas').append(`<option value="${kelas.kelas}">${kelas.kelas}</option>`);
                            });
                        } else {
                            $('#kelas').append('<option value="" disabled>Tidak Ada</option>');
                        }
                    }
                });
            }
        });

        $('#kelas').on('change', function () {
            const semesterId = $('#semester').val();
            const kelasKelas = $(this).val();

            $('#rombel').empty().append('<option value="" selected hidden disabled>Pilih Rombel</option>').prop('disabled', true);
            $('#ma_pel').empty().append('<option value="" selected hidden disabled>Pilih Mata Pelajaran</option>').prop('disabled', true);
            $('#jampel').empty().append('<option value="" selected hidden disabled>Pilih Jam Pelajaran</option>').prop('disabled', true);
            $('#btnTambahJadwal').prop('disabled', true);

            if (kelasKelas) {
                $.ajax({
                    url: '{{ route("jadwalmapel.ajaxHandler") }}',
                    type: 'GET',
                    data: {
                        action: 'getRombel',
                        semesterId: semesterId,
                        kelasKelas: kelasKelas,
                    },
                    success: function (data) {
                        $('#rombel').prop('disabled', false);
                        if (data.length > 0) {
                            data.forEach(kelas => {
                                $('#rombel').append(`<option value="${kelas.id}">${kelas.rombongan_belajar}</option>`);
                            });
                        } else {
                            $('#rombel').append('<option value="" disabled>Tidak Ada</option>');
                        }
                    }
                });
            }
        });

        $('#rombel').on('change', function () {
            const kelasId = $(this).val();

            $('#ma_pel').empty().append('<option value="" selected hidden disabled>Pilih Mata Pelajaran</option>').prop('disabled', true);
            $('#jampel').empty().append('<option value="" selected hidden disabled>Pilih Jam Pelajaran</option>').prop('disabled', true);
            $('#btnTambahJadwal').prop('disabled', true);
            
            if (kelasId) {
                refreshCalendar();
                $.ajax({
                    url: '{{ route("jadwalmapel.ajaxHandler") }}',
                    type: 'GET',
                    data: {
                        action: 'getMapel',
                        kelasId: kelasId,
                    },
                    success: function (data) {
                        $('#ma_pel').prop('disabled', false);
                        if (data.length > 0) {
                            data.forEach(mapel => {
                                $('#ma_pel').append(`<option value="${mapel.id}">${mapel.nama_mapel}${mapel.nama_guru ? ` - ${mapel.nama_guru}` : ''}</option>`);
                            });
                        } else {
                            $('#ma_pel').append('<option value="" disabled>Tidak Ada</option>');
                        }
                    }
                });
            }
        });

        function loadJampel() {
            const mapelkelasId = $('#ma_pel').val();

            $('#jampel').empty().append('<option value="" selected hidden disabled>Pilih Jam Pelajaran</option>').prop('disabled', true);
            $('#btnTambahJadwal').prop('disabled', true);
            
            if (mapelkelasId) {
                $.ajax({
                    url: '{{ route("jadwalmapel.ajaxHandler") }}',
                    type: 'GET',
                    data: {
                        action: 'getJampel',
                        mapelkelasId: mapelkelasId,
                    },
                    success: function (data) {
                        $('#jampel').prop('disabled', false);
                        if (data.length > 0) {
                            data.forEach(jampel => {
                                $('#jampel').append(`<option value="${jampel.id}" ${(jampel.rombongan_belajar || jampel.booked) ? 'disabled' : ''}>${jampel.hari} Jam ke-${jampel.nomor} | ${jampel.jam_mulai.substring(0, 5)} - ${jampel.jam_selesai.substring(0, 5)} ${jampel.rombongan_belajar ? '-> ' + jampel.rombongan_belajar : ''}</option>`);
                            });
                        } else {
                            $('#jampel').append('<option value="" disabled>Tidak Ada</option>');
                        }
                    }
                });
            }
        }

        $('#ma_pel').on('change', function () {
            loadJampel();
        });
        
        $('#jampel').on('change', function () {
            $('#btnTambahJadwal').prop('disabled', false);
        });
        
        function refreshCalendar() {
            const rombelId = $('#rombel').val();

            // Kirim request AJAX
            $.ajax({
                url: '{{ route("jadwalmapel.get-calendar") }}',
                type: 'GET',
                data: {
                    rombelId: rombelId
                },
                success: function (data) {
                    $('#schedule').jqs('reset');
                    $('#schedule').jqs('import', data);
                    $('.ui-draggable').each(function() {
                        $(this).off('mousedown');
                    });
                }
            });
        }

        $('#btnTambahJadwal').on('click', function () {
            const maPelValue = $('#ma_pel').val();
            const jamPelValue = $('#jampel').val();

            // Kirim request AJAX
            $.ajax({
                url: '{{ route("jadwalmapel.store") }}', // Ganti dengan URL endpoint Anda
                type: 'POST',
                data: {
                    mapelkelasId: maPelValue,
                    jampelId: jamPelValue
                },
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function (response) {
                    loadJampel();
                    refreshCalendar();
                    Swal.fire({
                        title: "Berhasil!",
                        text: response.message,
                        icon: "success",
                        timer: 2000, // Waktu dalam milidetik (3000 = 3 detik)
                        showConfirmButton: false
                    });
                }
            });
        });

        $('#btnShowCalendar').on('click', function () {
            refreshCalendar();
        });

        $('#btnHapusJPMK').on('click', function () {
            const jpmkValue = $('#jpmk-id').val();

            $.ajax({
                url: '{{ route("jadwalmapel.delete") }}',
                type: 'POST',
                data: {
                    jpmkId: jpmkValue
                },
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function (response) {
                    loadJampel();
                    refreshCalendar();
                    Swal.fire({
                        title: "Berhasil!",
                        text: response.message,
                        icon: "success",
                        timer: 2000,
                        showConfirmButton: false
                    });
                }
            });
        });

        $('#schedule').jqs({
            mode: 'edit',
            hour: 24,
            days: 7,
            periodDuration: 15,
            daysList: [
                'Senin',
                'Selasa',
                'Rabu',
                'Kamis',
                'Jumat',
                'Sabtu',
                'Minggu',
            ],
            periodOptions: true,
            periodRemoveButton: 'Hapus',
            onInit: function () {},
            onAddPeriod: function () {},
            onRemovePeriod: function () {},
            onDuplicatePeriod: function () {},
            onClickPeriod: function onPeriodClicked(event, period, jqs) {
                let jpmk_id = (event.target.innerHTML.match(/<span[^>]*jpmk-id="([^"]+)"/) || [])[1];
                if (jpmk_id) {
                    document.querySelector('#jpmk-hari').innerHTML = (event.target.innerHTML.match(/<span[^>]*hari="([^"]+)"/) || [])[1];
                    document.getElementById('showTinyCalendar').innerHTML = event.delegateTarget.innerHTML;
                    document.querySelector('#jpmk-id').value = jpmk_id;
                    document.getElementById('modalButton').click();
                }
            }
        });
    });
</script>
@endrole

@role('Guru|Wali Kelas')
<script>
    $(document).ready(function () {
        $('#schedule').jqs({
            mode: 'read',
            hour: 24,
            days: 7,
            periodDuration: 15,
            daysList: [
                'Senin',
                'Selasa',
                'Rabu',
                'Kamis',
                'Jumat',
                'Sabtu',
                'Minggu',
            ],
            periodOptions: true,
            periodRemoveButton: 'Hapus'
        });

        $.ajax({
            url: '{{ route("jadwalmapel.get-calendar-guru") }}',
            type: 'GET',
            success: function (data) {
                $('#schedule').jqs('reset');
                $('#schedule').jqs('import', data);
            }
        });
    });
</script>
@endrole

@role('Siswa')
<script>
    $(document).ready(function () {
        $('#schedule').jqs({
            mode: 'read',
            hour: 24,
            days: 7,
            periodDuration: 15,
            daysList: [
                'Senin',
                'Selasa',
                'Rabu',
                'Kamis',
                'Jumat',
                'Sabtu',
                'Minggu',
            ],
            periodOptions: true,
            periodRemoveButton: 'Hapus'
        });

        $.ajax({
            url: '{{ route("jadwalmapel.get-calendar-siswa") }}',
            type: 'GET',
            success: function (data) {
                $('#schedule').jqs('reset');
                $('#schedule').jqs('import', data);
            }
        });
    });
</script>
@endrole
@endpush