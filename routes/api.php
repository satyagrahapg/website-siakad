<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\SiswaController;
use App\Http\Controllers\KalenderAkademikController;
use App\Http\Controllers\KalenderMapelController;
use App\Http\Controllers\GuruController;
use App\Http\Controllers\SemesterController;
use App\Http\Controllers\KelasController;
use App\Http\Controllers\MapelController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\SemesterSelectionController;
use App\Http\Controllers\SillabusController;
use App\Http\Controllers\PenilaianController;
use App\Http\Controllers\PesertaDidikController;
use App\Http\Controllers\HalamanSiswaController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Auth\ForgotPasswordController;

Route::middleware('guest')->group(function () {
    Route::post('login', [LoginController::class, 'login'])->name('post_login');
    Route::controller(ForgotPasswordController::class)->group(function() {
        Route::post('forget-password', 'submitForgetPasswordForm')->name('forget.password.post'); 
        Route::post('reset-password', 'submitResetPasswordForm')->name('reset.password.post');
    });
});

Route::middleware('auth')->group(function () {
    Route::post('role', [LoginController::class, 'set_role'])->name('post_role');
    Route::post('logout', [LoginController::class, 'logout'])->name('logout');
});

Route::middleware(['auth', 'check_role'])->group(function () {
    Route::post('select-semester', [SemesterSelectionController::class, 'selectSemester'])->name('select.semester');
    Route::post('profile/update-picture', [UserController::class, 'update_picture'])->name('update_picture');
    Route::post('profile/update-password', [UserController::class, 'update_password'])->name('update_password');
    Route::get('kalender-akademik', [KalenderAkademikController::class, 'listEvent'])->name('kalenderakademik.list');

    Route::middleware('role:Admin|Super Admin')->group(function () {
        Route::prefix('accounts')->controller(AccountController::class)->group(function () {
            Route::post('{id}/delete', 'destroy')->name('account.destroy');
            Route::put('{id}', 'update')->name('account.update');
        });

        Route::prefix('siswas')->controller(SiswaController::class)->group(function () {
            Route::post('import', 'import')->name('siswa.import');
            Route::get('export', 'export')->name('siswa.export');
            Route::get('import', 'showImportForm')->name('siswa.showImportForm');
            Route::post('{id}/generate-user', 'generateUser')->name('siswa.generateUser');
            Route::put('store', 'store')->name('siswa.store');
            Route::put('update/{siswaId}', 'update')->name('siswa.update');
            Route::delete('delete/{siswaId}', 'delete')->name('siswa.delete');
        });

        Route::prefix('guru')->controller(GuruController::class)->group(function() {
            Route::post('import', 'import')->name('guru.import');
            Route::get('export', 'export')->name('guru.export');                
            Route::post('create', 'create')->name('guru.create');                              
            Route::put('{id}/update', 'update')->name('guru.update');           
            Route::delete('{id}', 'destroy')->name('guru.destroy');              
            Route::post('{guruId}/generate-user', 'generateUser')->name('guru.generateUser'); 
            Route::post('{guruId}/edit-role', 'editRole')->name('guru.editRole'); 
        });

        Route::prefix('staffs')->controller(AdminController::class)->group(function() {
            Route::post('import', 'import')->name('admin.import');                
            Route::get('export', 'export')->name('admin.export');                
            Route::post('create', 'create')->name('admin.create');                              
            Route::put('{id}/update', 'update')->name('admin.update');           
            Route::delete('{id}', 'destroy')->name('admin.destroy');              
            Route::post('{guruId}/generate-user', 'generateUser')->name('admin.generateUser'); 
        });
    });

    Route::middleware('role:Admin')->group(function () {
        Route::prefix('kelas')->controller(KelasController::class)->group(function () {
            Route::post('store', 'store')->name('kelas.store');
            Route::post('storeEkskul', 'storeEkskul')->name('kelas.storeEkskul');
            Route::post('{kelasId}/update', 'update')->name('kelas.update');
            Route::post('{kelasId}/add-student', 'addStudentToClass')->name('kelas.addStudent');
            Route::post('{kelasId}/hapus', 'hapusKelas')->name('kelas.hapus');
            Route::get('{kelasId}/export', 'exportKelas')->name('kelas.export');
            Route::delete('{kelasId}/siswa/{siswaId}', 'deleteAssignedSiswa')->name('kelas.siswa.delete');;
            Route::post('{kelasId}/auto-assign', 'autoAddStudents')->name('kelas.autoAdd');
            Route::post('{kelasId}/import-from-kelas', 'importSiswaFromKelas')->name('kelas.importFromKelas');
            Route::get('getKelasBySemester', 'getKelas')->name('kelas.getKelas');
        });

        Route::prefix('mapel')->controller(MapelController::class)->group(function () {
            Route::post('store', 'store')->name('mapel.store');
            Route::delete('{mapelId}/delete', 'hapusMapel')->name('mapel.delete');
            Route::post('{mapelId}/assign-kelas', 'assignKelasToMapel')->name('mapel.assign-kelas');
            Route::get('getMapelBySemester', 'getMapelBySemester')->name('mapel.getMapelBySemester');
        });

        Route::prefix('kalender-akademik')->controller(KalenderAkademikController::class)->group(function() {      
            Route::post('fetchKalender', 'ajax')->name('kalenderakademik.ajax');
        });

        Route::prefix('kalender')->controller(KalenderMapelController::class)->group(function() {
            Route::get('ajaxhandler', 'indexAjaxHandler')->name('kalendermapel.ajaxHandler');
            Route::post('store', 'storeMapelJampel')->name('kalendermapel.store');
            Route::post('delete', 'deleteMapelJampel')->name('kalendermapel.delete');
            Route::get('data-calendar', 'getDataCalendar')->name('kalendermapel.get-calendar');
            Route::post('jam-pelajaran/store', 'storeJampel')->name('kalendermapel.store-jampel');
            Route::delete('jam-pelajaran/{jampelId}/delete', 'hapusJampel')->name('kalendermapel.delete-jampel');
            Route::put('jam-pelajaran/{jampelId}/update', 'updateJampel')->name('kalendermapel.update-jampel');
            Route::post('get-kelas-by-mapel', 'getKelasByMapel')->name('kalendermapel.ajax');
        });

        Route::prefix('semesters')->controller(SemesterController::class)->group(function () {
            Route::post('/', 'store')->name('semesters.store');
            Route::put('{id}', 'update')->name('semesters.update');
            Route::delete('{id}', 'destroy')->name('semesters.destroy');
        });
    });

    Route::middleware('role:Guru|Wali Kelas')->group(function () {
        Route::prefix('kalender')->controller(KalenderMapelController::class)->group(function() {
            Route::get('data-calendar-guru', 'getDataCalendarGuru')->name('kalendermapel.get-calendar-guru');
        });

        Route::prefix('cp')->controller(SillabusController::class)->group(function () {
            Route::post('{mapelId}/store', 'storeCP')->name('silabus.storeCP');
            Route::post('{mapelId}/update/{cpId}', 'updateCP')->name('silabus.updateCP');
            Route::delete('{mapelId}/delete/{cpId}', 'deleteCP')->name('silabus.deleteCP');
        });

        Route::prefix('tp')->controller(SillabusController::class)->group(function () {
            Route::post('{mapelId}/cp/{cpId}/store','storeTP')->name('silabus.storeTP');
            Route::post('{mapelId}/cp/{cpId}/{tpId}/update', 'updateTP')->name('silabus.updateTP');
            Route::delete('{mapelId}/cp/{cpId}/{tpId}/delete', 'deleteTP')->name('silabus.deleteTP');
        });

        Route::prefix('penilaian/{mapelKelasId}')->controller(PenilaianController::class)->group(function () {
            Route::post('store', 'storePenilaian')->name('penilaian.store');
            Route::put('{penilaianId}/update', 'updatePenilaian')->name('penilaian.update');
            Route::delete('{penilaianId}/delete', 'deletePenilaian')->name('penilaian.delete');
            Route::put('update', 'updatePenilaianSiswaBatch')->name('penilaian.updateBatch');
        });

        Route::prefix('penilaian/ekskul/{kelasId}/{mapelId}')->controller(PenilaianController::class)->group(function () {
            Route::post('update', 'updateAllPenilaianEkskul')->name('penilaian.ekskul.update.all');
        });
    });

    Route::middleware('role:Wali Kelas')->group(function () {
        Route::prefix('peserta-didik')->controller(PesertaDidikController::class)->group(function () {
            Route::post('attendance/fetch', 'fetchAttendance')->name('pesertadidik.fetchAttendance');
            Route::post('attendance/save', 'saveAttendanceAjax')->name('pesertadidik.saveAttendanceAjax');
            Route::post('attendance/remove', 'removeAttendanceAjax')->name('pesertadidik.removeAttendanceAjax');
            Route::post('generate-rapor', 'generateRapotPDF')->name('pesertadidik.generateRapot');
            Route::post('fetch-p5bk', 'fetchP5BK')->name('p5bk.fetch');
            Route::post('save/{semesterId}', 'saveP5BKAjax')->name('p5bk.save');
        });
    });

    Route::middleware('role:Siswa')->group(function () {
        Route::prefix('siswa')->controller(KalenderMapelController::class)->group(function () {
            Route::get('data-calendar-siswa', 'getDataCalendarSiswa')->name('kalendermapel.get-calendar-siswa');
        });
        
        Route::prefix('siswa')->controller(HomeController::class)->group(function () {
            Route::get('fetch-kehadiran-semester', 'getKetidakHadiranChartData')->name('fetchKehadiranSemester');
        });

        Route::prefix('siswa')->controller(HalamanSiswaController::class)->group(function () {
            Route::get('fetch-buku-nilai','fetchBukuNilai')->name('fetchBukuNilai');
        });
    });
});