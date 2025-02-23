<div class="modal fade" data-bs-backdrop="static" id="createSiswaModal" tabindex="-1" aria-labelledby="createSiswaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createiswaModalLabel">Tambah Data Siswa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <!-- <span aria-hidden="true">&times;</span> -->
                </button>
            </div>
            <form action="{{ route('siswa.store') }}" method="POST" class="">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label for="nama_create">Nama</label>
                        <input type="text" name="nama" id="nama_create" class="form-control" placeholder="Nama" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="nisn_create">NISN</label>
                        <input type="text" name="nisn" id="nisn_create" class="form-control" pattern="^(?:\d{10})$" title="NISN harus terdiri dari tepat 10 digit." placeholder="NISN" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="tempat_lahir_create">Tempat Lahir</label>
                        <input type="text" name="tempat_lahir" id="tempat_lahir_create" class="form-control" placeholder="Tempat Lahir" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="tanggal_lahir_create">Tanggal Lahir</label>
                        <input type="date" name="tanggal_lahir" id="tanggal_lahir_create" class="form-control" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="jenis_kelamin_create">Jenis Kelamin</label>
                        <select name="jenis_kelamin" id="jenis_kelamin_create" class="form-control" required>
                            <option value="" selected disabled hidden>Pilih Jenis Kelamin</option>
                            <option value="Laki-laki">Laki-laki</option>
                            <option value="Perempuan">Perempuan</option>
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label for="agama_create">Agama</label>
                        <select name="agama" id="agama_create" class="form-select" required>
                            <option value="" selected disabled hidden>Pilih Agama</option>
                            <option value="Islam">Islam</option>
                            <option value="Kristen">Kristen</option>
                            <option value="Katolik">Katolik</option>
                            <option value="Hindu">Hindu</option>
                            <option value="Buddha">Buddha</option>
                            <option value="Konghucu">Konghucu</option>
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label for="status_keluarga_create">Status Keluarga</label>
                        <input type="text" name="status_keluarga" id="status_keluarga_create" class="form-control" placeholder="Status Keluarga">
                    </div>
                    <div class="form-group mb-3">
                        <label for="anak_ke_create">Anak Ke-</label>
                        <input type="number" name="anak_ke" id="anak_ke_create" class="form-control" placeholder="Anak Ke-">
                    </div>
                    <div class="form-group mb-3">
                        <label for="alamat_create">Alamat</label>
                        <textarea name="alamat_lengkap" id="alamat_create" class="form-control" placeholder="Alamat" required></textarea>
                    </div>
                    <div class="form-group mb-3">
                        <label for="telepon_create">No Telepon </label>
                        <input type="text" name="no_telepon" id="telepon_create" class="form-control" placeholder="Nomor Telepon" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="asal_sekolah_create">Asal Sekolah</label>
                        <input type="text" name="asal_sekolah" id="asal_sekolah_create" class="form-control" placeholder="Asal Sekolah">
                    </div>
                    <div class="form-group mb-3">
                        <label for="tanggal_diterima_create">Tanggal Diterima</label>
                        <input type="date" name="tanggal_diterima" id="tanggal_diterima_create" class="form-control">
                    </div>
                    <div class="form-group mb-3">
                        <label for="jalur_penerimaan_create">Jalur Penerimaan</label>
                        <input type="text" name="jalur_penerimaan" id="jalur_penerimaan_create" class="form-control" placeholder="Jalur Penerimaan">
                    </div>
                    <div class="form-group mb-3">
                        <label for="nama_ayah_create">Nama Ayah</label>
                        <input type="text" name="nama_ayah" id="nama_ayah_create" class="form-control" placeholder="Nama Ayah">
                    </div>
                    <div class="form-group mb-3">
                        <label for="pekerjaan_ayah_create">Pekerjaan Ayah</label>
                        <input type="text" name="pekerjaan_ayah" id="pekerjaan_ayah_create" class="form-control" placeholder="Pekerjaan Ayah">
                    </div>
                    <div class="form-group mb-3">
                        <label for="nama_ibu_create">Nama Ibu</label>
                        <input type="text" name="nama_ibu" id="nama_ibu_create" class="form-control" placeholder="Nama Ibu">
                    </div>
                    <div class="form-group mb-3">
                        <label for="pekerjaan_ibu_create">Pekerjaan Ibu</label>
                        <input type="text" name="pekerjaan_ibu" id="pekerjaan_ibu_create" class="form-control" placeholder="Pekerjaan Ibu">
                    </div>
                    <div class="form-group mb-3">
                        <label for="nama_wali_create">Nama Wali</label>
                        <input type="text" name="nama_wali" id="nama_wali_create" class="form-control" placeholder="Nama Wali">
                    </div>
                    <div class="form-group mb-3">
                        <label for="pekerjaan_wali_create">Pekerjaan Wali</label>
                        <input type="text" name="pekerjaan_wali" id="pekerjaan_wali_create" class="form-control" placeholder="Pekerjaan Wali">
                    </div>
                    <div class="form-group mb-3">
                        <label for="angkatan_create">Angkatan</label>
                        <input type="number" name="angkatan" id="angkatan_create" class="form-control" placeholder="Angkatan" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="width: 6rem">Tutup</button>
                    <button type="submit" class="btn btn-primary" style="width: 6rem">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>