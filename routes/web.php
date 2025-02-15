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
use App\Http\Controllers\KomentarController;
use App\Http\Controllers\PesertaDidikController;
use App\Http\Controllers\HalamanSiswaController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Auth\ForgotPasswordController;

// Group routes for LoginController using Route::controller
Route::middleware('guest')->controller(LoginController::class)->group(function () {
    Route::get('/', 'showLoginForm')->name('root');
    Route::get('login', 'showLoginForm')->name('login');
    Route::post('login', 'login')->name('post_login');
});

Route::middleware('auth')->group(function () {
    Route::get('role', [LoginController::class, 'select_role'])->name('role');
    Route::post('role', [LoginController::class, 'set_role'])->name('post_role');
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
});

// Group routes that require 'auth' middleware
Route::middleware(['auth', 'check_role'])->group(function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home');
    Route::get('/profile', [UserController::class, 'profile'])->name('profile');
    Route::post('/profile/update-picture', [UserController::class, 'update_picture'])->name('update_picture');
    Route::post('/profile/update-password', [UserController::class, 'update_password'])->name('update_password');
    Route::get('/kalender', [KalenderMapelController::class, 'index'])->name('kalendermapel.index');

    Route::prefix('kalender-akademik')->controller(KalenderAkademikController::class)->group(function() {
        Route::get('/', 'index')->name('kalenderakademik.index');              
        Route::get('/list', 'listEvent')->name('kalenderakademik.list');       
    });

    //Ini untuk nge-protect routes biar khusus cuma diakses sama Admin
    Route::middleware('role:Admin|Super Admin')->group(function () {

        // Account CRUD routes
        Route::prefix('accounts')->controller(AccountController::class)->group(function () {
            Route::get('/', 'index')->name('account.index');
            Route::get('/{id}/edit', 'edit')->name('account.edit');
            Route::post('/{id}/delete', 'destroy')->name('account.destroy');
            Route::put('/{id}', 'update')->name('account.update');
        });

        // Siswa data routes
        Route::prefix('siswas')->controller(SiswaController::class)->group(function () {
            Route::get('/', 'index')->name('siswa.index');
            Route::post('/import', 'import')->name('siswa.import');
            Route::get('/export', 'export')->name('siswa.export');
            Route::get('/import', 'showImportForm')->name('siswa.showImportForm');
            Route::post('/{id}/generate-user', 'generateUser')->name('siswa.generateUser');
            Route::put('/store', 'store')->name('siswa.store');
            Route::put('/update/{siswaId}', 'update')->name('siswa.update');
            Route::delete('/delete/{siswaId}', 'delete')->name('siswa.delete');

        });

        // Guru data routes
        Route::prefix('guru')->controller(GuruController::class)->group(function() {
            Route::get('/', 'index')->name('guru.index');                         
            Route::post('/import', 'import')->name('guru.import');
            Route::get('/export', 'export')->name('guru.export');                
            Route::post('/create', 'create')->name('guru.create');                              
            Route::put('/{id}/update', 'update')->name('guru.update');           
            Route::delete('/{id}', 'destroy')->name('guru.destroy');              
            Route::post('/{guruId}/generate-user', 'generateUser')->name('guru.generateUser'); 
            Route::post('/{guruId}/edit-role', 'editRole')->name('guru.editRole'); 
        });

        // Admin data routes
        Route::prefix('staffs')->controller(AdminController::class)->group(function() {
            Route::get('/', 'index')->name('admin.index');                         
            Route::post('/import', 'import')->name('admin.import');                
            Route::get('/export', 'export')->name('admin.export');                
            Route::post('/create', 'create')->name('admin.create');                              
            Route::put('/{id}/update', 'update')->name('admin.update');           
            Route::delete('/{id}', 'destroy')->name('admin.destroy');              
            Route::post('/{guruId}/generate-user', 'generateUser')->name('admin.generateUser'); 
        });
    });

    Route::middleware('role:Admin')->group(function () {
        Route::prefix('kelas')->controller(KelasController::class)->group(function () {
            Route::get('/', 'index')->name('kelas.index');
            // Route::get('/create', 'create')->name('kelas.create');
            Route::post('/store', 'store')->name('kelas.store');
            Route::post('/storeEkskul', 'storeEkskul')->name('kelas.storeEkskul');
            Route::post('/{kelasId}/update', 'update')->name('kelas.update');
            Route::post('/{kelasId}/add-student', 'addStudentToClass')->name('kelas.addStudent');
            Route::get('/{kelasId}/buka', 'bukaKelas')->name('kelas.buka');
            Route::post('/{kelasId}/hapus', 'hapusKelas')->name('kelas.hapus');
            Route::get('/{kelasId}/export', 'exportKelas')->name('kelas.export');
            Route::delete('/kelas/{kelasId}/siswa/{siswaId}', 'deleteAssignedSiswa')->name('kelas.siswa.delete');;
            Route::post('/{kelasId}/auto-assign', 'autoAddStudents')->name('kelas.autoAdd');
            Route::post('/{kelasId}/import-from-kelas', 'importSiswaFromKelas')->name('kelas.importFromKelas');
            Route::get('getKelasBySemester/', 'getKelas')->name('kelas.getKelas');
        });

        //Mapel routes
        Route::prefix('mapel')->controller(MapelController::class)->group(function () {
            Route::get('/', 'index')->name('mapel.index');
            Route::post('/store', 'store')->name('mapel.store');
            Route::delete('/{mapelId}/delete', 'hapusMapel')->name('mapel.delete');
            Route::post('/{mapelId}/assign-kelas', 'assignKelasToMapel')->name('mapel.assign-kelas');
            Route::get('/getMapelBySemester', 'getMapelBySemester')->name('mapel.getMapelBySemester');
        });

        // Kalender Akademik routes
        Route::prefix('kalender-akademik')->controller(KalenderAkademikController::class)->group(function() {      
            Route::post('/ajax', 'ajax')->name('kalenderakademik.ajax');
        });

        Route::prefix('kalender')->controller(KalenderMapelController::class)->group(function() {
            Route::get('/ajaxhandler', 'indexAjaxHandler')->name('kalendermapel.ajaxHandler');
            Route::post('/store', 'storeMapelJampel')->name('kalendermapel.store');
            Route::post('/delete', 'deleteMapelJampel')->name('kalendermapel.delete');
            Route::get('/data-calendar', 'getDataCalendar')->name('kalendermapel.get-calendar');
            Route::get('/jam-pelajaran', 'showJampel')->name('kalendermapel.index-jampel');
            Route::post('/jam-pelajaran/store', 'storeJampel')->name('kalendermapel.store-jampel');
            Route::delete('/jam-pelajaran/{jampelId}/delete', 'hapusJampel')->name('kalendermapel.delete-jampel');
            Route::put('/jam-pelajaran/{jampelId}/update', 'updateJampel')->name('kalendermapel.update-jampel');
            Route::post('/get-kelas-by-mapel', 'getKelasByMapel')->name('kalendermapel.ajax');
        });

        // Semester routes
        Route::prefix('semesters')->controller(SemesterController::class)->group(function () {
            Route::get('/', 'index')->name('semesters.index');
            Route::post('/', 'store')->name('semesters.store');
            Route::put('/{id}', 'update')->name('semesters.update');
            Route::delete('/{id}', 'destroy')->name('semesters.destroy');
        });
    });

    //disini kita protect Routesnya Guru yak!

    Route::middleware('role:Guru|Wali Kelas')->group(function () {
        Route::get('/data-calendar-guru', [KalenderMapelController::class, 'getDataCalendarGuru'])->name('kalendermapel.get-calendar-guru');

        Route::prefix('cp')->controller(SillabusController::class)->group(function () {
            Route::get('/{mapelId}', 'index')->name('silabus.index');
            Route::post('/{mapelId}/store', 'storeCP')->name('silabus.storeCP');
            Route::post('/{mapelId}/update/{cpId}', 'updateCP')->name('silabus.updateCP');
            Route::delete('/{mapelId}/delete/{cpId}', 'deleteCP')->name('silabus.deleteCP');
        });

        Route::prefix('tp')->controller(SillabusController::class)->group(function () {
            Route::get('/{mapelId}/cp/{cpId}', 'bukaTP')->name('bukaTP');
            Route::post('/{mapelId}/cp/{cpId}/store','storeTP')->name('silabus.storeTP');
            Route::post('/{mapelId}/cp/{cpId}/{tpId}/update', 'updateTP')->name('silabus.updateTP');
            Route::delete('/{mapelId}/cp/{cpId}/{tpId}/delete', 'deleteTP')->name('silabus.deleteTP');
        });

        Route::prefix('penilaian/{mapelKelasId}')->controller(PenilaianController::class)->group(function () {
            Route::get('/', 'index')->name('penilaian.index');
            Route::post('/store', 'storePenilaian')->name('penilaian.store');
            Route::put('/{penilaianId}/update', 'updatePenilaian')->name('penilaian.update');
            Route::delete('/{penilaianId}/delete', 'deletePenilaian')->name('penilaian.delete');
            Route::get('/buka/{penilaianId}','bukaPenilaian')->name('penilaian.buka');
            Route::put('/update', 'updatePenilaianSiswaBatch')->name('penilaian.updateBatch');
            Route::get('/buku-nilai', 'bukuNilai')->name('penilaian.bukuNilai');
        });

        Route::prefix('penilaian/ekskul/{kelasId}/{mapelId}')->controller(PenilaianController::class)->group(function () {
            Route::get('/',  'penilaianEkskul')->name('penilaian.ekskul');
            Route::post('/update', 'updateAllPenilaianEkskul')->name('penilaian.ekskul.update.all');
        });

        Route::prefix('komentar/{mapelId}')->controller(KomentarController::class)->group(function () {
            Route::get('/', 'index')->name('komentar.index');
            Route::post('/update', 'updateKomentar')->name('komentar.update');
        });
    });

    Route::middleware('role:Wali Kelas')->group(function () {
        Route::post('/generate-rapor', [PesertaDidikController::class, 'generateRapotPDF'])->name('pesertadidik.generateRapot');

        Route::prefix('peserta-didik')->controller(PesertaDidikController::class)->group(function () {
            Route::post('/attendance/fetch', 'fetchAttendance')->name('pesertadidik.fetchAttendance');
            Route::post('/attendance/save', 'saveAttendanceAjax')->name('pesertadidik.saveAttendanceAjax');
            Route::post('/attendance/remove', 'removeAttendanceAjax')->name('pesertadidik.removeAttendanceAjax');
            Route::get('/buku-absen/{semesterId}', 'bukuAbsen')->name('pesertadidik.bukuAbsen');
            Route::get('/{semesterId}', 'index')->name('pesertadidik.index');
            Route::get('/leger-nilai/{kelasId}/{semesterId}', 'bukaLegerNilai')->name('pesertadidik.legerNilai');
            Route::get('/attendance-index/{semesterId}', 'attendanceIndex')->name('pesertadidik.attendanceIndex');
            Route::get('/save-attendance', 'attendanceIndex')->name('pesertadidik.storeAttendance');
            Route::get('/p5bk/{semesterId}', 'p5bkIndex')->name('p5bk.index');
            Route::post('fetch-p5bk', 'fetchP5BK')->name('p5bk.fetch');
            Route::post('save/{semesterId}', 'saveP5BKAjax')->name('p5bk.save');
        });
    });

    
    Route::middleware('role:Siswa')->group(function () {
        Route::get('/data-calendar-siswa', [KalenderMapelController::class, 'getDataCalendarSiswa'])->name('kalendermapel.get-calendar-siswa');

        Route::get('/fetch-kehadiran-semester',[HomeController::class,'getKetidakHadiranChartData'])->name('fetchKehadiranSemester');

        Route::prefix('siswa')->controller(HalamanSiswaController::class)->group(function () {
            Route::get('/absensi', 'absensi')->name('siswapage.absensi');
            Route::get('/nilai', 'bukuNilaiSiswa')->name('siswapage.bukunilai');
            Route::get('/fetch-buku-nilai','fetchBukuNilai')->name('fetchBukuNilai');
        });
    });
});

// Default route redirect to login page

//Route unntuk reset password
Route::get('forget-password', [ForgotPasswordController::class, 'showForgetPasswordForm'])->name('forget.password.get');
Route::post('forget-password', [ForgotPasswordController::class, 'submitForgetPasswordForm'])->name('forget.password.post'); 
Route::get('reset-password/{token}', [ForgotPasswordController::class, 'showResetPasswordForm'])->name('reset.password.get');
Route::post('reset-password', [ForgotPasswordController::class, 'submitResetPasswordForm'])->name('reset.password.post');

Route::post('/select-semester', [SemesterSelectionController::class, 'selectSemester'])->name('select.semester');