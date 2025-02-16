@extends('layout.layout')

@push('style')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.bootstrap5.css">
@endpush

@section('content')
<div class="container-fluid mt-3">
    <div class="card mb-3 border-0 shadow-sm" style="background-color:#f2f2f2;">
        <div class="card-body" style="background-color: #37B7C3; border-radius: 8px">
            <h2 class="m-0" style="color: #EBF4F6">Presensi {{ optional($kelasSemester)->rombongan_belajar ? '| '.$kelasSemester->rombongan_belajar: '' }}</h2>
        </div>
    </div>

    <!-- Date Selection -->
    <div class="row mb-3">
        <div class="col-md-4">
            <label for="date">Pilih Tanggal:</label>
            <input type="date" id="date" class="form-control" value="{{ \Carbon\Carbon::today()->toDateString() }}">
        </div>
    </div>
    <button id="saveAttendance" class="btn btn-primary mb-3">Simpan Presensi</button>
    <button id="removeAttendance" class="btn btn-danger mb-3 mx-1">Hapus Presensi</button>

    <!-- buku absensi -->
    <button class="btn btn-warning mb-3">
        <a href="{{ route('pesertadidik.bukuAbsen', $semesterId) }}" class="text-decoration-none text-black">
            Buku Presensi
        </a>
    </button>

    

    <!-- Attendance Table -->
    <div class="table-responsive mb-3">
        <table id="example" class="attendance-table table table-striped" style="width:100%">
            <thead>
                <tr>
                    <th class="text-start">No</th>
                    <th class="text-start">Nama</th>
                    <th class="text-start">NISN</th>
                    {{-- <th>Rombongan Belajar</th> --}}
                    <th class="text-start">Status</th>
                    <th class="text-start">Terakhir Diperbarui</th>
                    <th class="text-start">Presensi</th>
                </tr>
            </thead>
            <tbody>
                <!-- Rows will be populated via AJAX -->
            </tbody>
        </table>
    </div>

</div>

<!-- Success and Error Alerts -->
<div id="alertMessage" class="mt-3" style="display: none;"></div>
@endsection

@push('script')
    
<!-- Scripts -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const dateInput = document.getElementById('date');
        const saveButton = document.getElementById('saveAttendance');
        const removeButton = document.getElementById('removeAttendance');
        const tableBody = document.querySelector('.attendance-table tbody');
        const alertDiv = document.getElementById('alertMessage');
        const semesterId = {{ $semesterId }}; // Passed from the controller

        function showAlert(message, type = 'success') {
            alertDiv.style.display = 'block';
            alertDiv.className = `alert alert-${type}`;
            alertDiv.textContent = message;

            setTimeout(() => {
                alertDiv.style.display = 'none';
            }, 3000);
        }

        // Fetch Attendance Data
        async function fetchAttendance() {
            const date = dateInput.value;

            if (!date) {
                showAlert('Please select a date.', 'warning');
                return;
            }

            try {
                const response = await fetch('{{ route("pesertadidik.fetchAttendance") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ semester_id: semesterId, date: date })
                });

                const data = await response.json();

                if (data.success) {
                    // Populate Table
                    tableBody.innerHTML = '';
                    data.students.forEach((student, index) => {
                        const status = data.attendance[student.id] || 'hadir';
                        const update = data.last_update[student.id] || 'Tidak Tersedia';
                        const statusLabel = {
                                hadir: 'Hadir',
                                ijin: 'Izin',
                                sakit: 'Sakit',
                                terlambat: 'Terlambat',
                                alpha: 'Alpa'
                            }[data.attendance[student.id]] || 'Not Set';
                        if (!data.attendance[student.id]) removeButton.classList.add('disabled');
                        else removeButton.classList.remove('disabled');
                        const row = `
                            <tr>
                                <td class="text-start">${index + 1}</td>
                                <td class="text-start">${student.nama}</td>
                                <td class="text-start">${student.nisn}</td>
                                <td class="text-start">${statusLabel}</td>
                                <td class="text-start">${update}</td>
                                <td class="text-start">
                                    <select name="attendance[${student.id}]" class="form-control form-select">
                                        <option value="hadir" ${status === 'hadir' ? 'selected' : ''}>Hadir</option>
                                        <option value="ijin" ${status === 'ijin' ? 'selected' : ''}>Izin</option>
                                        <option value="sakit" ${status === 'sakit' ? 'selected' : ''}>Sakit</option>
                                        <option value="terlambat" ${status === 'terlambat' ? 'selected' : ''}>Terlambat</option>
                                        <option value="alpha" ${status === 'alpha' ? 'selected' : ''}>Alpa</option>
                                        
                                    </select>
                                </td>
                            </tr>
                        `;
                        tableBody.insertAdjacentHTML('beforeend', row);
                    });
                } else {
                    showAlert('Failed to fetch attendance.', 'danger');
                }
            } catch (error) {
                console.error('Error fetching attendance:', error);
                showAlert('An error occurred while fetching attendance.', 'danger');
            }
        }

        // Save Attendance Data
        async function saveAttendance() {
            const date = dateInput.value;

            const attendance = {};
            document.querySelectorAll('.attendance-table tbody select').forEach(select => {
                const studentId = select.name.match(/\d+/)[0]; // Extract ID from name
                attendance[studentId] = select.value;
            });

            try {
                const response = await fetch('{{ route("pesertadidik.saveAttendanceAjax") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ semester_id: semesterId, date: date, attendance: attendance })
                });

                const data = await response.json();

                if (data.success) {
                    // Show success message
                    fetchAttendance();
                    Swal.fire({
                        title: "Berhasil!",
                        text: data.message || "Presensi harian berhasil disimpan!",
                        icon: "success",
                        timer: 1500,
                        showConfirmButton: false
                    });
                } else {
                    // Show failure message from the server
                    Swal.fire({
                        title: "Failed!",
                        text: data.message || "Presensi harian gagal disimpan, silahkan coba lagi!",
                        icon: "error"
                    });
                }
            } catch (error) {
                // Show error alert
                Swal.fire({
                    title: "Error!",
                    text: "Terjadi kesalahan saat menyimpan presensi, silahkan coba lagi!",
                    icon: "error"
                });
            }
        }

        //Remove attendance data
        async function removeAttendance() {
            const date = dateInput.value;

            const attendance = [];
            document.querySelectorAll('.attendance-table tbody select').forEach(select => {
                const studentId = select.name.match(/\d+/)[0]; // Extract ID from name
                attendance.push(studentId);
            });

            try {
                const response = await fetch('{{ route("pesertadidik.removeAttendanceAjax") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ semester_id: semesterId, date: date, attendance: attendance })
                });

                const data = await response.json();

                if (data.success) {
                    // Show success message
                    fetchAttendance();
                    Swal.fire({
                        title: "Berhasil!",
                        text: data.message || "Presensi harian berhasil dihapus!",
                        icon: "success",
                        timer: 1500,
                        showConfirmButton: false
                    });
                } else {
                    // Show failure message from the server
                    Swal.fire({
                        title: "Failed!",
                        text: data.message || "Presensi harian gagal dihapus, silahkan coba lagi!",
                        icon: "error"
                    });
                }
            } catch (error) {
                // Show error alert
                Swal.fire({
                    title: "Error!",
                    text: "Terjadi kesalahan saat menghapus presensi, silahkan coba lagi!",
                    icon: "error"
                });
            }
        }

        // Event Listeners
        dateInput.addEventListener('change', fetchAttendance);
        saveButton.addEventListener('click', saveAttendance);
        removeButton.addEventListener('click', removeAttendance);

        // Initial Fetch
        fetchAttendance();
    });
</script>

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
            scrollY: 440,
            language: {
                url: "{{ asset('style/js/bahasa.json') }}" // Ganti dengan path ke file bahasa Anda
            }
        });
    });
</script>
@endpush