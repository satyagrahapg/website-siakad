<div class="modal fade" data-bs-backdrop="static" id="createGuruModal" tabindex="-1" aria-labelledby="createGuruModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createGuruModalLabel">Tambah Pendidik</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('pendidik.create') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <!-- Form fields for Guru data -->
                    <div class="form-group mb-3">
                        <label for="nama">Nama</label>
                        <input type="text" name="nama" class="form-control" required placeholder="Nama">
                    </div>
                    <div class="form-group mb-3">
                        <label for="nip">NIP / Kode Pegawai</label>
                        <input type="text" name="nip" class="form-control" pattern="^(?:\d{11}|\d{18})$" title="NIP atau Kode Pegawai harus terdiri dari tepat 11 atau 18 digit." required placeholder="NIP / Kode Pegawai">
                    </div>
                    <div class="form-group mb-3">
                        <label for="gelar_depan">Gelar Depan</label>
                        <input type="text" name="gelar_depan" id="gelar_depan" class="form-control" placeholder="Gelar Depan">
                    </div>

                    <div class="form-group mb-3">
                        <label for="gelar_belakang">Gelar Belakang</label>
                        <input type="text" name="gelar_belakang" id="gelar_belakang" class="form-control" placeholder="Gelar Belakang">
                    </div>
                    <div class="form-group mb-3">
                        <label for="tempat_lahir">Tempat Lahir</label>
                        <input type="text" name="tempat_lahir" class="form-control" maxlength="255" required placeholder="Tempat Lahir">
                    </div>
                    <div class="form-group mb-3">
                        <label for="tanggal_lahir">Tanggal Lahir</label>
                        <input type="date" name="tanggal_lahir" class="form-control" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="jenis_kelamin">Jenis Kelamin</label>
                        <select name="jenis_kelamin" class="form-select" required>
                            <option value="" selected disabled hidden>Pilih Jenis Kelamin</option>
                            <option value="Laki-laki">Laki-laki</option>
                            <option value="Perempuan">Perempuan</option>
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label for="agama">Agama</label>
                        <select name="agama" class="form-select" required>
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
                        <label for="alamat">Alamat</label>
                        <input type="text" name="alamat" class="form-control" maxlength="255" required placeholder="Alamat">
                    </div>
                    <div class="form-group mb-3">
                        <label for="jabatan">Jabatan</label>
                        <input type="text" name="jabatan" class="form-control" maxlength="255" required placeholder="Jabatan">
                    </div>
                    <div class="form-group mb-3">
                        <label for="status">Status</label>
                        <select name="status" id="status-option" class="form-select" required>
                            <option value="" selected disabled hidden>Pilih Status</option>
                            <option value="PNS">PNS</option>
                            <option value="PPPK">PPPK</option>
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label for="pangkat_golongan">Pangkat Golongan</label>
                        <select name="pangkat_golongan" id="golongan-option" class="form-select" required>
                            <option value="" selected disabled hidden>Pilih Pangkat Golongan</option>
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label for="pendidikan">Pendidikan</label>
                        <input type="text" name="pendidikan" class="form-control" maxlength="50" required placeholder="Pendidikan">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>