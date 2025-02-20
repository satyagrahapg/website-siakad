@extends('layout/layout')

@push('style')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.bootstrap5.css">
    <style>
        .text-content {
            margin-bottom: 0px;
            font-size: 1.1rem;
        }
    </style>
@endpush

@section('content')
    @role('Guru|Wali Kelas|Admin|Super Admin|Siswa')
        <div class="container-fluid mt-3">
            <div class="card mb-3 border-0 shadow-sm" style="background-color:#f2f2f2;">
                <div class="card-body" style="background-color: #37B7C3; border-radius: 8px">
                    @role('Super Admin')
                    <h2 class="m-0 text-center" style="color: #EBF4F6">Selamat Datang di SIAKAD, Super Admin {{ auth()->user()->name }}</h2>
                    @endrole
                    @role('Admin')
                    <h2 class="m-0 text-center" style="color: #EBF4F6">Selamat Datang di SIAKAD, Admin {{ auth()->user()->name }}</h2>
                    @endrole
                    @role('Guru')
                    <h2 class="m-0 text-center" style="color: #EBF4F6">Selamat Datang di SIAKAD, Guru {{ auth()->user()->name }}</h2>
                    @endrole
                    @role('Wali Kelas')
                    <h2 class="m-0 text-center" style="color: #EBF4F6">Selamat Datang di SIAKAD, Wali Kelas {{ auth()->user()->name }}</h2>
                    @endrole
                    @role('Siswa')
                    <h2 class="m-0 text-center" style="color: #EBF4F6">Selamat Datang di SIAKAD, Siswa {{ auth()->user()->name }}</h2>
                    @endrole
                </div>
            </div>

            <div class="mb-4">
                <div class="row mb-4">
                    <!-- Main Dashboard -->
                    <div class="col-6">
                        <div class="h-100">
                            <div class="card h-100">
                                <div class="card-body p-5">
                                    <div class="row justify-content-center mb-4">
                                        <img class="rounded-circle mb-3" style="object-fit: cover; height: 250px; width: auto;" src="{{asset('style/assets/sekolah1.png')}}" alt="sekolah-bro">
                                        <h3 class="text-center" style="color: #1e1e1e; font-size: 1.5rem; font-weight: 600;">SMP Negeri 1 Karangawen</h3>
                                    </div>
                                    <p class="card-text text-content" style="font-size: 1.15rem">Kurikulum : Kurikulum Merdeka</p>
                                    <p class="card-text text-content" style="font-size: 1.15rem">Akreditasi : A</p>
                                    <h5 class="card-title mt-4" style="font-size: 1.15rem">Semester Aktif</h5>
                                    @foreach($semesterAktif as $semester)
                                    <p class="card-text text-content" style="font-size: 1.15rem">Tahun Ajaran : {{ $semester->semester}} | {{$semester->tahun_ajaran}}</p>
                                    @endforeach
                                    <h5 class="card-title mt-3" style="font-size: 1.15rem">Kepala Sekolah</h5>
                                    @foreach($kepalaSekolah as $kepala)
                                    <p class="card-text text-content ml-2">{{ $kepala->nama }}</p>
                                    @endforeach
                                    {{-- <h5 class="card-title mt-3" style="font-size: 1.15rem">Operator</h5>
                                    @if($operator->isNotEmpty())
                                        <ul style="padding-left: 0rem;">
                                            @foreach($operator as $op)
                                                <li style="font-size: 1.1rem"> {{ $op->nama }}</li>
                                            @endforeach
                                        </ul>
                                    @else
                                        <p class="card-text text-content">-</p>
                                    @endif --}}
                                </div>
                            </div>
                        </div>
                    </div>
                    @role('Admin|Super Admin')
                        <div class="col-6">
                            <div class="h-100">
                            {{-- Charts --}}
                            <div class="">
                                <div class="card mb-4 p-3" >
                                    <h4 class="text-center">Distribusi Tenaga Kependidikan</h4>
                                    <canvas id="tenagaKependidikanChart" height="300px"></canvas>
                                </div>
                            </div>
                            {{-- Charts --}}
                            <div class="">
                                <div class="card p-3" >
                                    <h4 class="text-center">Distribusi Tenaga Pendidik</h4>
                                    <canvas id="pendidikChart" height="300px"></canvas>
                                </div>
                            </div>
                            </div>
                        </div>
                    @endrole
                    @role('Guru|Wali Kelas')
                        <div class="col-6">
                            <div class="h-100 row">
                                    <div class="col-6">
                                        <div class="h-100">
                                            <div class="card mb-4" style="height: 90%;">
                                                <div class="card-body d-flex flex-column justify-content-between">
                                                    <div class="d-flex justify-content-between">
                                                        <h5 class="card-title mb-3">Total Mata<br>Pelajaran Diampu</h5>
                                                        <i class="fa-solid fa-chalkboard-user fa-2xl" style="margin-top: 32px;"></i>
                                                    </div>
                                                    <h5 class="card-text">{{ $totalMapel }}</h5>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="h-100">
                                            <div class="card mb-4" style="height: 90%;">
                                                <div class="card-body d-flex flex-column justify-content-between">
                                                    <div class="d-flex justify-content-between">
                                                        <h5 class="card-title mb-3">Total Rombongan<br>Belajar Diampu</h5>
                                                        <i class="fa-solid fa-users fa-2xl" style="margin-top: 32px;"></i>
                                                    </div>
                                                    <h5 class="card-text">{{ $totalRombel }}</h5>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="h-100">
                                            <div class="card mb-4" style="height: 90%;">
                                                <div class="card-body d-flex flex-column justify-content-between">
                                                    <div class="d-flex justify-content-between">
                                                        <h5 class="card-title mb-3">Total Capaian<br>Pembelajaran</h5>
                                                        <i class="fa-solid fa-bullseye fa-2xl" style="margin-top: 32px;"></i>
                                                    </div>
                                                    <h5 class="card-text">{{ $totalCP }}</h5>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="h-100">
                                            <div class="card mb-4" style="height: 90%;">
                                                <div class="card-body d-flex flex-column justify-content-between">
                                                    <div class="d-flex justify-content-between">
                                                        <h5 class="card-title mb-3">Total Tujuan<br>Pembelajaran</h5>
                                                        <i class="fa-solid fa-graduation-cap fa-2xl" style="margin-top: 32px;"></i>
                                                    </div>
                                                    <h5 class="card-text">{{ $totalTP }}</h5>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="h-100">
                                            <div class="card mb-4" style="height: 90%;">
                                                <div class="card-body d-flex flex-column justify-content-between">
                                                    <div class="d-flex justify-content-between">
                                                        <h5 class="card-title mb-3">Total Tugas<br>Peserta Didik</h5>
                                                        <i class="fa-solid fa-book fa-2xl" style="margin-top: 32px;"></i>
                                                    </div>
                                                    <h5 class="card-text">{{ $totalTugas }}</h5>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="h-100">
                                            <div class="card mb-4" style="height: 90%;">
                                                <div class="card-body d-flex flex-column justify-content-between">
                                                    <div class="d-flex justify-content-between">
                                                        <h5 class="card-title mb-3">Total Ulangan<br>Harian</h5>
                                                        <i class="fa-solid fa-calendar-check fa-2xl" style="margin-top: 32px;"></i>
                                                    </div>
                                                    <h5 class="card-text">{{ $totalUH }}</h5>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="h-100">
                                            <div class="card mb-4" style="height: 90%;">
                                                <div class="card-body d-flex flex-column justify-content-between">
                                                    <div class="d-flex justify-content-between">
                                                        <h5 class="card-title mb-3">Total Sumatif<br>Tengah Semester</h5>
                                                        <i class="fa-solid fa-check fa-2xl" style="margin-top: 32px;"></i>
                                                    </div>
                                                    <h5 class="card-text">{{ $totalSTS }}</h5>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="h-100">
                                            <div class="card mb-4" style="height: 90%;">
                                                <div class="card-body d-flex flex-column justify-content-between">
                                                    <div class="d-flex justify-content-between">
                                                        <h5 class="card-title mb-3">Total Sumatif<br>Akhir Semester</h5>
                                                        <i class="fa-solid fa-check-double fa-2xl" style="margin-top: 32px;"></i>
                                                    </div>
                                                    <h5 class="card-text">{{ $totalSAS }}</h5>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="h-100">
                                            <div class="card mb-4" style="height: 90%;">
                                                <div class="card-body d-flex flex-column justify-content-between">
                                                    <div class="d-flex justify-content-between">
                                                        <h5 class="card-title mb-3">Total Ekstrakurikuler</h5>
                                                        <i class="fa-solid fa-person-swimming fa-2xl" style="margin-top: 32px;"></i>
                                                    </div>
                                                    <h5 class="card-text">{{ $totalEkskul }}</h5>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    @role('Wali Kelas')
                                        <div class="col-6">
                                            <div class="h-100">
                                                <div class="card mb-4" style="height: 90%;">
                                                    <div class="card-body d-flex flex-column justify-content-between">
                                                        <div class="d-flex justify-content-between">
                                                            <h5 class="card-title mb-3">Total Siswa Perwalian</h5>
                                                            <i class="fa-solid fa-hands-holding-child fa-2xl" style="margin-top: 32px;"></i>
                                                        </div>
                                                        <h5 class="card-text">{{ $totalPerwalian }}</h5>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endrole
                            </div>
                        </div>
                    @endrole
                    @role('Siswa')
                        <div class="col-6 h-100">
                            <div class="h-100">
                                {{-- Charts --}}
                                <div class="row g-3">
                                    <div class="col-12">
                                        <div class="card p-3 h-100">
                                            <h4 class="text-center">Rekap Ketidakhadiran</h4>
                                            <canvas id="tenagaKependidikanChart" height="300px"></canvas>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="card p-3 h-100">
                                            <div class="d-flex justify-content-between">
                                                <div class="d-flex flex-column justify-content-between">
                                                    <p class="card-title mb-3" style="font-size:0.85rem"><a class="stretched-link text-decoration-none text-dark" href="{{ route('siswapage.absensi') }}">Cek Presensi</a></p>
                                                    <h5 class="card-text">Ayo Jangan Terlambat</h5>
                                                </div>
                                                <i class="fa-solid fa-caret-right fa-2xl" style="margin-top: 32px;"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="card p-3 h-100">
                                            <div class="d-flex justify-content-between">
                                                <div class="d-flex flex-column justify-content-between">
                                                    <p class="card-title mb-3" style="font-size:0.85rem"><a href="{{ route('siswapage.bukunilai') }}" class="stretched-link text-decoration-none text-dark">Cek Buku Nilai</a></p>
                                                    <h5 class="card-text">Belajar Yang Rajin</h5>
                                                </div>
                                                <i class="fa-solid fa-caret-right fa-2xl" style="margin-top: 32px;"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="card p-3 h-100">
                                            <div class="d-flex justify-content-between">
                                                <div class="d-flex flex-column justify-content-between">
                                                    <p class="card-title mb-3" style="font-size:0.85rem"><a href="{{ route('jadwalmapel.index') }}" class="stretched-link text-decoration-none text-dark">Cek Jadwal Pembelajaran</a></p>
                                                    <h5 class="card-text">Jangan Lupa Jadwalnya</h5>
                                                </div>
                                                <i class="fa-solid fa-caret-right fa-2xl" style="margin-top: 32px;"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="card p-3 h-100">
                                            <div class="d-flex justify-content-between">
                                                <div class="d-flex flex-column justify-content-between">
                                                    <p class="card-title mb-3" style="font-size:0.85rem"><a href="{{ route('kalenderakademik.index') }}" class="stretched-link text-decoration-none text-dark">Cek Kalender Akademik</a></p>
                                                    <h5 class="card-text">Cek Agenda Terbaru</h5>
                                                </div>
                                                <i class="fa-solid fa-caret-right fa-2xl" style="margin-top: 32px;"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                {{-- Charts --}}
                                <div class="row row-cols-2 g-3">
                                    
                                </div>
                            </div>
                        </div>
                    @endrole
                </div>
                <div class="row">
                    @role('Admin|Super Admin')
                        <div class="col-3">
                            <div class="h-100">
                                <div class="card mb-4" style="height: 90%;">
                                    <div class="card-body d-flex flex-column justify-content-between">
                                        <div class="d-flex justify-content-between">
                                            <h5 class="card-title mb-3">Total Pendidik</h5>
                                            <i class="fa-solid fa-chalkboard-user fa-2xl" style="margin-top: 32px;"></i>
                                        </div>
                                        <h5 class="card-text">{{ $totalGuru }}</h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="h-100">
                                <div class="card mb-4" style="height: 90%;">
                                    <div class="card-body d-flex flex-column justify-content-between">
                                        <div class="d-flex justify-content-between">
                                            <h5 class="card-title mb-3">Total Admin</h5>
                                            <i class="fa-solid fa-user-pen fa-2xl" style="margin-top: 32px;"></i>
                                        </div>
                                        <h5 class="card-text">{{ $totalOperator }}</h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="h-100">
                                <div class="card mb-4" style="height: 90%;">
                                    <div class="card-body d-flex flex-column justify-content-between">
                                        <div class="d-flex justify-content-between">
                                            <h5 class="card-title mb-3">Total Tenaga<br>Kependidikan</h5>
                                            <i class="fa-solid fa-user-tie fa-2xl" style="margin-top: 32px;"></i>
                                        </div>
                                        <h5 class="card-text">{{ $totalAdmin }}</h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="h-100">
                                <div class="card mb-4" style="height: 90%;">
                                    <div class="card-body d-flex flex-column justify-content-between">
                                        <div class="d-flex justify-content-between">
                                            <h5 class="card-title mb-3">Total Peserta<br>Didik</h5>
                                            <i class="fa-solid fa-graduation-cap fa-2xl" style="margin-top: 32px;"></i>
                                        </div>
                                        <h5 class="card-text">{{ $totalSiswa }}</h5>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @role('Admin')
                            <div class="col-3">
                                <div class="h-100">
                                    <div class="card mb-4" style="height: 90%;">
                                        <div class="card-body d-flex flex-column justify-content-between">
                                            <div class="d-flex justify-content-between">
                                                <h5 class="card-title mb-3">Total Kelas</h5>
                                                <i class="fa-solid fa-door-open fa-2xl" style="margin-top: 32px;"></i>
                                            </div>
                                            <h5 class="card-text">{{ $totalKelas }}</h5>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-3">
                                <div class="h-100">
                                    <div class="card mb-4" style="height: 90%;">
                                        <div class="card-body d-flex flex-column justify-content-between">
                                            <div class="d-flex justify-content-between">
                                                <h5 class="card-title mb-3">Total Mata Pelajaran</h5>
                                                <i class="fa-solid fa-book-open fa-2xl" style="margin-top: 32px;"></i>
                                            </div>
                                            <h5 class="card-text">{{ $totalMapel }}</h5>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-3">
                                <div class="h-100">
                                    <div class="card mb-4" style="height: 90%;">
                                        <div class="card-body d-flex flex-column justify-content-between">
                                            <div class="d-flex justify-content-between">
                                                <h5 class="card-title mb-3">Total Ekstrakurikuler</h5>
                                                <i class="fa-solid fa-person-swimming fa-2xl" style="margin-top: 32px;"></i>
                                            </div>
                                            <h5 class="card-text">{{ $totalEkskul }}</h5>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endrole
                    @endrole
                </div>
            </div>
        </div>
    @endrole
@endsection
@push('script')
    {{-- chartjs cdn --}}
    @role('Admin|Super Admin')
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.5/dist/chart.umd.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Get data from Laravel
                let tenagaKependidikanData = @json($tenagaKependidikanChartData);
                let pendidikData = @json($pendidikChartData);   
                console.log(typeof tenagaKependidikanData);

                // Extract labels and values for tenaga kependidikan
                let tenagaLabels = ["Tenaga Kebersihan", "Tenaga Keamanan", "Tata Usaha"];
                let tenagaValues = [
                    tenagaKependidikanData.tenaga_kebersihan,
                    tenagaKependidikanData.tenaga_keamanan,
                    tenagaKependidikanData.tata_usaha
                ];

                // Extract labels and values for pendidik
                let pendidikLabels = ["PNS", "PPPK", "PTT", "GTT"];
                let pendidikValues = [
                    pendidikData.pns,
                    pendidikData.pppk,
                    pendidikData.ptt,
                    pendidikData.gtt
                ];

                // Create Tenaga Kependidikan Chart
                new Chart(document.getElementById("tenagaKependidikanChart"), {
                    type: 'bar',
                    data: {
                        labels: tenagaLabels,
                        datasets: [{
                            label: 'Jumlah',
                            data: tenagaValues,
                            backgroundColor: [
                                'rgba(255, 99, 132, 0.6)',
                                'rgba(255, 159, 64, 0.6)',
                                'rgba(255, 205, 86, 0.6)'],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1, // Pastikan hanya menampilkan angka bulat
                                    precision: 0, // Hindari angka desimal
                                }
                            }
                        }
                    }
                });

                // Create Pendidik Chart
                new Chart(document.getElementById("pendidikChart"), {
                    type: 'bar',
                    data: {
                        labels: pendidikLabels,
                        datasets: [{
                            label: 'Jumlah',
                            data: pendidikValues,
                            backgroundColor: [
                                'rgba(75, 192, 192, 0.6)',
                                'rgba(54, 162, 235, 0.6)',
                                'rgba(153, 102, 255, 0.6)',
                                'rgba(201, 203, 207, 0.6)'],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1, // Pastikan hanya menampilkan angka bulat
                                    precision: 0, // Hindari angka desimal
                                }
                            }
                        }
                    }
                });
            });
        </script>
    @endrole
    @role('Siswa')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.5/dist/chart.umd.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script>
        // Declare chart variable
        let chart = null;

        // Function to draw chart
        function drawChart(ketidakhadiranData = null) {
            // Extract labels and values for ketidakhadiran
            let ketidakhadiranLabels = ["Sakit", "Izin", "Alpa", "Terlambat"];
            let ketidakhadiranValues = [
                ketidakhadiranData.sakit,
                ketidakhadiranData.izin,
                ketidakhadiranData.alpa,
                ketidakhadiranData.terlambat,
            ];

            // Create Ketidakhadiran Chart
            if (chart) {
                chart.destroy();
            }
            chart = new Chart(document.getElementById("tenagaKependidikanChart"), {
                type: 'bar',
                data: {
                    labels: ketidakhadiranLabels,
                    datasets: [{
                        label: 'Jumlah',
                        data: ketidakhadiranValues,
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.6)',
                            'rgba(255, 159, 64, 0.6)',
                            'rgba(255, 205, 86, 0.6)',
                            'rgba(75, 192, 192, 0.6)'], 
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1, // Pastikan hanya menampilkan angka bulat
                                precision: 0, // Hindari angka desimal
                            }
                        }
                    }
                }
            });
        }
        document.addEventListener('DOMContentLoaded', function() {
            
            // Get data from Laravel via ajax
            $.ajax({
                url: "{{ route('fetchKehadiranSemester') }}",
                type: "GET",
                success: function(response) {
                    console.log(response.message);
                    // console.log(session)
                    drawChart(response.data);
                },
                error: function(xhr) {
                    console.log(xhr);
                }
            });


        }) 
    </script>
    @endrole
@endpush