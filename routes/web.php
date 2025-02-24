<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\SiswaController;
use App\Http\Controllers\KalenderAkademikController;
use App\Http\Controllers\JadwalMapelController;
use App\Http\Controllers\PendidikController;
use App\Http\Controllers\SemesterController;
use App\Http\Controllers\KelasController;
use App\Http\Controllers\MapelController;
use App\Http\Controllers\TendikController;
use App\Http\Controllers\SemesterSelectionController;
use App\Http\Controllers\SilabusController;
use App\Http\Controllers\PenilaianController;
use App\Http\Controllers\WaliKelasController;
use App\Http\Controllers\HalamanSiswaController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Auth\ForgetPasswordController;

app()->router->group(['prefix' => 'api'], function () {
    require base_path('routes/api.php');
});

//Routes Login
Route::middleware('guest')->group(function () {
    Route::controller(LoginController::class)->group(function() {
        Route::get('/', 'showLoginForm')->name('root');
        Route::get('login', 'showLoginForm')->name('login');
    });

    //Routes Forget PW
    Route::controller(ForgetPasswordController::class)->group(function() {
        Route::get('forget-password', 'showForgetPasswordForm')->name('forget.password.get');       
        Route::get('reset-password/{token}', 'showResetPasswordForm')->name('reset.password.get');    
    });
});

Route::middleware('auth')->group(function () {
    Route::get('role', [LoginController::class, 'select_role'])->name('role');
});

Route::middleware(['auth', 'check_role'])->group(function () {
    Route::get('home', [HomeController::class, 'index'])->name('home');
    Route::get('profile', [UserController::class, 'profile'])->name('profile');
    Route::get('jadwal', [JadwalMapelController::class, 'index'])->name('jadwalmapel.index');
    Route::get('kalender-akademik', [KalenderAkademikController::class, 'index'])->name('kalenderakademik.index');                      
    
    Route::middleware('role:Admin|Super Admin')->group(function () {
        Route::prefix('accounts')->controller(AccountController::class)->group(function () {
            Route::get('/', 'index')->name('account.index');
        });

        Route::prefix('siswas')->controller(SiswaController::class)->group(function () {
            Route::get('/', 'index')->name('siswa.index');
        });

        Route::prefix('pendidik')->controller(PendidikController::class)->group(function() {
            Route::get('/', 'index')->name('pendidik.index');
        });

        Route::prefix('staffs')->controller(TendikController::class)->group(function() {
            Route::get('/', 'index')->name('tendik.index');
        });
    });

    Route::middleware('role:Admin')->group(function () {
        Route::prefix('kelas')->controller(KelasController::class)->group(function () {
            Route::get('/', 'index')->name('kelas.index');
            Route::get('{kelasId}/buka', 'bukaKelas')->name('kelas.buka');
        });

        Route::prefix('mapel')->controller(MapelController::class)->group(function () {
            Route::get('/', 'index')->name('mapel.index');
        });

        Route::prefix('jadwal')->controller(JadwalMapelController::class)->group(function() {
            Route::get('jam-pelajaran', 'showJampel')->name('jadwalmapel.index-jampel');
        });

        Route::prefix('semesters')->controller(SemesterController::class)->group(function () {
            Route::get('/', 'index')->name('semesters.index');
        });
    });

    Route::middleware('role:Guru|Wali Kelas')->group(function () {
        Route::prefix('cp')->controller(SilabusController::class)->group(function () {
            Route::get('{mapelId}', 'index')->name('silabus.index');
        });

        Route::prefix('tp')->controller(SilabusController::class)->group(function () {
            Route::get('{mapelId}/cp/{cpId}', 'bukaTP')->name('bukaTP');
        });

        Route::prefix('penilaian/{mapelKelasId}')->controller(PenilaianController::class)->group(function () {
            Route::get('/', 'index')->name('penilaian.index');
            Route::get('buka/{penilaianId}','bukaPenilaian')->name('penilaian.buka');
            Route::get('buku-nilai', 'bukuNilai')->name('penilaian.bukuNilai');
        });

        Route::prefix('penilaian/ekskul/{kelasId}/{mapelId}')->controller(PenilaianController::class)->group(function () {
            Route::get('/',  'penilaianEkskul')->name('penilaian.ekskul');
        });
    });

    Route::middleware('role:Wali Kelas')->group(function () {
        Route::prefix('peserta-didik')->controller(WaliKelasController::class)->group(function () {
            Route::get('buku-absen/{semesterId}', 'bukuAbsen')->name('pesertadidik.bukuAbsen');
            Route::get('{semesterId}', 'index')->name('pesertadidik.index');
            Route::get('leger-nilai/{kelasId}/{semesterId}', 'bukaLegerNilai')->name('pesertadidik.legerNilai');
            Route::get('attendance-index/{semesterId}', 'attendanceIndex')->name('pesertadidik.attendanceIndex');
            Route::get('p5bk/{semesterId}', 'p5bkIndex')->name('p5bk.index');
        });
    });

    Route::middleware('role:Siswa')->group(function () {
        Route::prefix('siswa')->controller(HalamanSiswaController::class)->group(function () {
            Route::get('absensi', 'absensi')->name('siswapage.absensi');
            Route::get('nilai', 'bukuNilaiSiswa')->name('siswapage.bukunilai');
        });
    });
});