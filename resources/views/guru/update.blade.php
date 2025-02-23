<!-- Ubah Guru Modal -->
<div class="modal fade" data-bs-backdrop="static" id="editGuruModal-{{ $guru->id }}" tabindex="-1" aria-labelledby="editGuruModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-lg modal-dialog-centeredsssss">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editGuruModalLabel">Ubah Guru {{ $guru->nama }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <!-- <span aria-hidden="true">&times;</span> -->
                </button>
            </div>
            <div class="modal-body">
                <form action="{{ route('guru.update', $guru->id) }}" method="POST" class="m-0">
                    @csrf
                    @method('PUT') <!-- Use PUT here to match REST conventions for updates -->
                    <div class="form-group mb-3">
                        <label for="nama">Nama</label>
                        <input type="text" name="nama" id="nama" class="form-control" value="{{ old('nama', $guru->nama) }}" required placeholder="Nama">
                    </div>

                    <div class="form-group mb-3">
                        <label for="nip">NIP / Kode Pegawai</label>
                        <input type="text" name="nip" id="nip" class="form-control" value="{{ old('nip', $guru->nip) }}" required placeholder="NIP / Kode Pegawai" pattern="^(?:\d{11}|\d{18})$" title="NIP atau Kode Pegawai harus terdiri dari tepat 11 atau 18 digit.">
                    </div>

                    <div class="form-group mb-3">
                        <label for="gelar_depan">Gelar Depan</label>
                        <input type="text" name="gelar_depan" id="gelar_depan" class="form-control" value="{{ old('gelar_depan', $guru->gelar_depan) }}" placeholder="Gelar Depan">
                    </div>

                    <div class="form-group mb-3">
                        <label for="gelar_belakang">Gelar Belakang</label>
                        <input type="text" name="gelar_belakang" id="gelar_belakang" class="form-control" value="{{ old('gelar_belakang', substr($guru->gelar_belakang, 2)) }}" placeholder="Gelar Belakang">
                    </div>

                    <div class="form-group mb-3">
                        <label for="tempat_lahir">Tempat Lahir</label>
                        <input type="text" name="tempat_lahir" id="tempat_lahir" class="form-control" value="{{ old('tempat_lahir', $guru->tempat_lahir) }}" required placeholder="Tempat Lahir">
                    </div>

                    <div class="form-group mb-3">
                        <label for="tanggal_lahir">Tanggal Lahir</label>
                        <input type="date" name="tanggal_lahir" id="tanggal_lahir" class="form-control" value="{{ old('tanggal_lahir', $guru->tanggal_lahir) }}" required>
                    </div>

                    <div class="form-group mb-3">
                        <label for="jenis_kelamin">Jenis Kelamin</label>
                        <select name="jenis_kelamin" id="jenis_kelamin" class="form-select" required>
                            <option value="" selected disabled hidden>Pilih Jenis Kelamin</option>
                            <option value="Laki-laki" {{ $guru->jenis_kelamin == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="Perempuan" {{ $guru->jenis_kelamin == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                    </div>

                    <div class="form-group mb-3">
                        <label for="agama">Agama</label>
                        <select name="agama" class="form-select" required>
                            <option value="" selected disabled hidden>Pilih Agama</option>
                            <option value="Islam" {{ old('agama', $guru->agama) == 'Islam' ? 'selected' : '' }}>Islam</option>
                            <option value="Kristen" {{ old('agama', $guru->agama) == 'Kristen' ? 'selected' : '' }}>Kristen</option>
                            <option value="Katolik" {{ old('agama', $guru->agama) == 'Katolik' ? 'selected' : '' }}>Katolik</option>
                            <option value="Hindu" {{ old('agama', $guru->agama) == 'Hindu' ? 'selected' : '' }}>Hindu</option>
                            <option value="Buddha" {{ old('agama', $guru->agama) == 'Buddha' ? 'selected' : '' }}>Buddha</option>
                            <option value="Konghucu" {{ old('agama', $guru->agama) == 'Konghucu' ? 'selected' : '' }}>Konghucu</option>
                        </select>
                    </div>

                    <div class="form-group mb-3">
                        <label for="alamat">Alamat</label>
                        <input type="text" name="alamat" id="alamat" class="form-control" value="{{ old('alamat', $guru->alamat) }}" required placeholder="Alamat">
                    </div>

                    <div class="form-group mb-3">
                        <label for="jabatan">Jabatan</label>
                        <input type="text" name="jabatan" id="jabatan" class="form-control" value="{{ old('jabatan', $guru->jabatan) }}" required placeholder="Jabatan">
                    </div>

                    <div class="form-group mb-3">
                        <label for="status">Status</label>
                        <select name="status" id="status-option" class="form-select" required>
                            <option value="" selected disabled hidden>Pilih Status</option>
                            <option value="PNS" {{ old('status', $guru->status) == 'PNS' ? 'selected' : '' }}>PNS</option>
                            <option value="PPPK" {{ old('status', $guru->status) == 'PPPK' ? 'selected' : '' }}>PPPK</option>
                        </select>
                    </div>

                    <div class="form-group mb-3">
                        <label for="pangkat_golongan">Pangkat Golongan</label>
                        <select name="pangkat_golongan" id="golongan-option" class="form-select" required>
                            <option value="" selected disabled hidden>Pilih Pangkat Golongan</option>
                            @if (old('status', $guru->status) == 'PNS')
                                <option value="III/a" {{ old('pangkat_golongan', $guru->pangkat_golongan) == 'III/a' ? 'selected' : '' }}>III/a</option>
                                <option value="III/b" {{ old('pangkat_golongan', $guru->pangkat_golongan) == 'III/b' ? 'selected' : '' }}>III/b</option>
                                <option value="III/c" {{ old('pangkat_golongan', $guru->pangkat_golongan) == 'III/c' ? 'selected' : '' }}>III/c</option>
                                <option value="III/d" {{ old('pangkat_golongan', $guru->pangkat_golongan) == 'III/d' ? 'selected' : '' }}>III/d</option>
                                <option value="IV/a" {{ old('pangkat_golongan', $guru->pangkat_golongan) == 'IV/a' ? 'selected' : '' }}>IV/a</option>
                                <option value="IV/b" {{ old('pangkat_golongan', $guru->pangkat_golongan) == 'IV/b' ? 'selected' : '' }}>IV/b</option>
                                <option value="IV/c" {{ old('pangkat_golongan', $guru->pangkat_golongan) == 'IV/c' ? 'selected' : '' }}>IV/c</option>
                                <option value="IV/d" {{ old('pangkat_golongan', $guru->pangkat_golongan) == 'IV/d' ? 'selected' : '' }}>IV/d</option>
                                <option value="IV/e" {{ old('pangkat_golongan', $guru->pangkat_golongan) == 'IV/e' ? 'selected' : '' }}>IV/e</option>
                            @elseif (old('status', $guru->status) == 'PPPK')
                                <option value="IX" {{ old('pangkat_golongan', $guru->pangkat_golongan) == 'IX' ? 'selected' : '' }}>IX</option>
                                <option value="X" {{ old('pangkat_golongan', $guru->pangkat_golongan) == 'X' ? 'selected' : '' }}>X</option>
                                <option value="XI" {{ old('pangkat_golongan', $guru->pangkat_golongan) == 'XI' ? 'selected' : '' }}>XI</option>
                                <option value="XII" {{ old('pangkat_golongan', $guru->pangkat_golongan) == 'XII' ? 'selected' : '' }}>XII</option>
                                <option value="XIII" {{ old('pangkat_golongan', $guru->pangkat_golongan) == 'XIII' ? 'selected' : '' }}>XIII</option>
                                <option value="XIV" {{ old('pangkat_golongan', $guru->pangkat_golongan) == 'XIV' ? 'selected' : '' }}>XIV</option>
                                <option value="XV" {{ old('pangkat_golongan', $guru->pangkat_golongan) == 'XV' ? 'selected' : '' }}>XV</option>
                                <option value="XVI" {{ old('pangkat_golongan', $guru->pangkat_golongan) == 'XVI' ? 'selected' : '' }}>XVI</option>
                                <option value="XVII" {{ old('pangkat_golongan', $guru->pangkat_golongan) == 'XVII' ? 'selected' : '' }}>XVII</option>
                            @endif
                        </select>
                    </div>

                    <div class="form-group mb-3">
                        <label for="pendidikan">Pendidikan</label>
                        <input type="text" name="pendidikan" id="pendidikan" class="form-control" value="{{ old('pendidikan', $guru->pendidikan) }}" required placeholder="Pendidikan">
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