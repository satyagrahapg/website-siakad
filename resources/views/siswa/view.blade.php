<!-- View Modal for each Siswa -->
<div class="modal fade" data-bs-backdrop="static" id="viewSiswaModal-{{ $siswa->id }}" tabindex="-1" aria-labelledby="viewSiswaModalLabel-{{ $siswa->id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewSiswaModalLabel-{{ $siswa->id }}">Lihat Data Siswa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <!-- <span aria-hidden="true">&times;</span> -->
                </button>
            </div>
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label for="nama">Nama</label>
                        <input type="text" name="nama" id="nama" class="form-control" value="{{ old('nama', $siswa->nama) }}" disabled>
                    </div>
                    <div class="form-group mb-3">
                        <label for="nisn">NISN</label>
                        <input type="text" name="nisn" id="nisn" class="form-control" value="{{ old('nisn', $siswa->nisn) }}" disabled>
                    </div>
                    <div class="form-group mb-3">
                        <label for="tempat_lahir">Tempat Lahir</label>
                        <input type="text" name="tempat_lahir" id="tempat_lahir" class="form-control" value="{{ old('tempat_lahir', $siswa->tempat_lahir) }}" disabled placeholder="-">
                    </div>
                    <div class="form-group mb-3">
                        <label for="tanggal_lahir">Tanggal Lahir</label>
                        <input type="date" name="tanggal_lahir" id="tanggal_lahir" class="form-control" value="{{ old('tanggal_lahir', $siswa->tanggal_lahir) }}" disabled>
                    </div>
                    <div class="form-group mb-3">
                        <label for="jenis_kelamin">Jenis Kelamin</label>
                        <select name="jenis_kelamin" id="jenis_kelamin" class="form-control" disabled>
                            <option value="" selected disabled hidden>Pilih Jenis Kelamin</option>
                            <option value="Laki-laki" {{ $siswa->jenis_kelamin == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="Perempuan" {{ $siswa->jenis_kelamin == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label for="agama">Agama</label>
                        <select name="agama" id="agama" class="form-select" required disabled>
                            <option value="" selected disabled hidden>Pilih Agama</option>
                            <option value="Islam" {{ old('agama', $siswa->agama) == 'Islam' ? 'selected' : '' }}>Islam</option>
                            <option value="Kristen" {{ old('agama', $siswa->agama) == 'Kristen' ? 'selected' : '' }}>Kristen</option>
                            <option value="Katolik" {{ old('agama', $siswa->agama) == 'Katolik' ? 'selected' : '' }}>Katolik</option>
                            <option value="Hindu" {{ old('agama', $siswa->agama) == 'Hindu' ? 'selected' : '' }}>Hindu</option>
                            <option value="Buddha" {{ old('agama', $siswa->agama) == 'Buddha' ? 'selected' : '' }}>Buddha</option>
                            <option value="Konghucu" {{ old('agama', $siswa->agama) == 'Konghucu' ? 'selected' : '' }}>Konghucu</option>
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label for="status_keluarga">Status Keluarga</label>
                        <input type="text" name="status_keluarga" id="status_keluarga" class="form-control" value="{{ old('status_keluarga', $siswa->status_keluarga) }}" disabled placeholder="-">
                    </div>
                    <div class="form-group mb-3">
                        <label for="anak_ke">Anak Ke</label>
                        <input type="number" name="anak_ke" id="anak_ke" class="form-control" value="{{ old('anak_ke', $siswa->anak_ke) }}" disabled placeholder="-">
                    </div>
                    <div class="form-group mb-3">
                        <label for="alamat">Alamat</label>
                        <textarea name="alamat" id="alamat" class="form-control" disabled placeholder="-">{{ old('alamat', $siswa->alamat) }}</textarea>
                    </div>
                    <div class="form-group mb-3">
                        <label for="telepon">No Telepon </label>
                        <input type="text" name="telepon" id="telepon" class="form-control" value="{{ old('telepon', $siswa->telepon) }}" disabled placeholder="-">
                    </div>
                    <div class="form-group mb-3">
                        <label for="asal_sekolah">Asal Sekolah</label>
                        <input type="text" name="asal_sekolah" id="asal_sekolah" class="form-control" value="{{ old('asal_sekolah', $siswa->asal_sekolah) }}" disabled placeholder="-">
                    </div>
                    <div class="form-group mb-3">
                        <label for="tanggal_diterima">Tanggal Diterima</label>
                        <input type="date" name="tanggal_diterima" id="tanggal_diterima" class="form-control" value="{{ old('tanggal_diterima', $siswa->tanggal_diterima) }}" disabled>
                    </div>
                    <div class="form-group mb-3">
                        <label for="jalur_penerimaan">Jalur Penerimaan</label>
                        <input type="text" name="jalur_penerimaan" id="jalur_penerimaan" class="form-control" value="{{ old('jalur_penerimaan', $siswa->jalur_penerimaan) }}" disabled placeholder="-">
                    </div>
                    <div class="form-group mb-3">
                        <label for="nama_ayah">Nama Ayah</label>
                        <input type="text" name="nama_ayah" id="nama_ayah" class="form-control" value="{{ old('nama_ayah', $siswa->nama_ayah) }}" disabled placeholder="-">
                    </div>
                    <div class="form-group mb-3">
                        <label for="pekerjaan_ayah">Pekerjaan Ayah</label>
                        <input type="text" name="pekerjaan_ayah" id="pekerjaan_ayah" class="form-control" value="{{ old('pekerjaan_ayah', $siswa->pekerjaan_ayah) }}" disabled placeholder="-">
                    </div>
                    <div class="form-group mb-3">
                        <label for="nama_ibu">Nama Ibu</label>
                        <input type="text" name="nama_ibu" id="nama_ibu" class="form-control" value="{{ old('nama_ibu', $siswa->nama_ibu) }}" disabled placeholder="-">
                    </div>
                    <div class="form-group mb-3">
                        <label for="pekerjaan_ibu">Pekerjaan Ibu</label>
                        <input type="text" name="pekerjaan_ibu" id="pekerjaan_ibu" class="form-control" value="{{ old('pekerjaan_ibu', $siswa->pekerjaan_ibu) }}" disabled placeholder="-">
                    </div>
                    <div class="form-group mb-3">
                        <label for="nama_wali">Nama Wali</label>
                        <input type="text" name="nama_wali" id="nama_wali" class="form-control" value="{{ old('nama_wali', $siswa->nama_wali) }}" disabled placeholder="-">
                    </div>
                    <div class="form-group mb-3">
                        <label for="pekerjaan_wali">Pekerjaan Wali</label>
                        <input type="text" name="pekerjaan_wali" id="pekerjaan_wali" class="form-control" value="{{ old('pekerjaan_wali', $siswa->pekerjaan_wali) }}" disabled placeholder="-">
                    </div>
                    <div class="form-group mb-3">
                        <label for="angkatan">Angkatan</label>
                        <input type="number" name="angkatan" id="angkatan" class="form-control" value="{{ old('angkatan', $siswa->angkatan) }}" disabled>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="width: 6rem">Tutup</button>
                    {{-- <button type="submit" class="btn btn-primary" style="width: 6rem">Simpan</button> --}}
                </div>
        </div>
    </div>
</div>