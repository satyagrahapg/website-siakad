<!-- Ubah Admin Modal -->
<div class="modal fade" id="editAdminModal-{{ $a->id }}" tabindex="-1" aria-labelledby="editAdminModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editAdminModalLabel">Ubah data {{ $a->nama }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.update', $a->id) }}" method="POST">
                @csrf
                @method('PUT') <!-- Use PUT here to match REST conventions for updates -->
                
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label for="nama">Nama</label>
                        <input type="text" name="nama" id="nama" class="form-control" value="{{ old('nama', $a->nama) }}" required>
                    </div>

                    <div class="form-group mb-3">
                        <label for="nip">NIP / Kode Pegawai</label>
                        <input type="text" name="nip" id="nip" class="form-control" value="{{ old('nip', $a->nip) }}" required>
                    </div>

                    <div class="form-group mb-3">
                        <label for="nip">Jabatan</label>
                        <select name="jabatan" class="form-select" required> 
                            <option value="Tata Usaha" {{ old('jabatan', $a->jabatan) == 'Tata Usaha' ? 'selected' : '' }}>Tata Usaha</option>
                            <option value="Tenaga Kebersihan" {{ old('jabatan', $a->jabatan) == 'Tenaga Kebersihan' ? 'selected' : '' }}>Tenaga Kebersihan</option>
                            <option value="Tenaga Keamanan" {{ old('jabatan', $a->jabatan) == 'Tenaga Keamanan' ? 'selected' : '' }}>Tenaga Keamanan</option>
                        </select>
                    </div>

                    <div class="form-group mb-3">
                        <label for="tempat_lahir">Tempat Lahir</label>
                        <input type="text" name="tempat_lahir" id="tempat_lahir" class="form-control" value="{{ old('tempat_lahir', $a->tempat_lahir) }}" required>
                    </div>

                    <div class="form-group mb-3">
                        <label for="tanggal_lahir">Tanggal Lahir</label>
                        <input type="date" name="tanggal_lahir" id="tanggal_lahir" class="form-control" value="{{ old('tanggal_lahir', $a->tanggal_lahir) }}" required>
                    </div>

                    <div class="form-group mb-3">
                        <label for="jenis_kelamin">Jenis Kelamin</label>
                        <select name="jenis_kelamin" id="jenis_kelamin" class="form-select" required>
                            <option value="Laki-laki" {{ $a->jenis_kelamin == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="Perempuan" {{ $a->jenis_kelamin == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                    </div>

                    <div class="form-group mb-3">
                        <label for="agama">Agama</label>
                        <select name="agama" class="form-select" required>
                            <option value="Islam" {{ old('agama', $a->agama) == 'Islam' ? 'selected' : '' }}>Islam</option>
                            <option value="Kristen" {{ old('agama', $a->agama) == 'Kristen' ? 'selected' : '' }}>Kristen</option>
                            <option value="Katolik" {{ old('agama', $a->agama) == 'Katolik' ? 'selected' : '' }}>Katolik</option>
                            <option value="Hindu" {{ old('agama', $a->agama) == 'Hindu' ? 'selected' : '' }}>Hindu</option>
                            <option value="Buddha" {{ old('agama', $a->agama) == 'Buddha' ? 'selected' : '' }}>Buddha</option>
                            <option value="Konghucu" {{ old('agama', $a->agama) == 'Konghucu' ? 'selected' : '' }}>Konghucu</option>
                        </select>
                    </div>

                    <div class="form-group mb-3">
                        <label for="alamat">Alamat</label>
                        <input type="text" name="alamat" id="alamat" class="form-control" value="{{ old('alamat', $a->alamat) }}" required>
                    </div>

                    <div class="form-group mb-3">
                        <label for="status">Status</label>
                        <select name="status" id="status-option" class="form-select" required>
                            <option value="PNS" {{ old('status', $a->status) == 'PNS' ? 'selected' : '' }}>PNS</option>
                            <option value="PPPK" {{ old('status', $a->status) == 'PPPK' ? 'selected' : '' }}>PPPK</option>
                            <option value="GTT" {{ old('status', $a->status) == 'GTT' ? 'selected' : '' }}>GTT</option>
                            <option value="PTT" {{ old('status', $a->status) == 'PTT' ? 'selected' : '' }}>PTT</option>
                        </select>
                    </div>

                    <div class="form-group mb-3">
                        <label for="pangkat_golongan" id="golongan-title" @if (strpos(old('status', $a->status), "TT")) {{ 'hidden' }} @endif>Pangkat Golongan</label>
                        <input type="hidden" name="pangkat_golongan" id="golongan-hidden" class="form-control"  @if (!strpos(old('status', $a->status), "TT")) {{ 'disabled' }} @endif>
                        <select name="pangkat_golongan" id="golongan-option" class="form-select" @if (strpos(old('status', $a->status), "TT")) {{ 'disabled hidden' }} @endif required>
                            @if (old('status', $a->status) == 'PNS')
                                <option value="III/a" {{ old('pangkat_golongan', $a->pangkat_golongan) == 'III/a' ? 'selected' : '' }}>III/a</option>
                                <option value="III/b" {{ old('pangkat_golongan', $a->pangkat_golongan) == 'III/b' ? 'selected' : '' }}>III/b</option>
                                <option value="III/c" {{ old('pangkat_golongan', $a->pangkat_golongan) == 'III/c' ? 'selected' : '' }}>III/c</option>
                                <option value="III/d" {{ old('pangkat_golongan', $a->pangkat_golongan) == 'III/d' ? 'selected' : '' }}>III/d</option>
                                <option value="IV/a" {{ old('pangkat_golongan', $a->pangkat_golongan) == 'IV/a' ? 'selected' : '' }}>IV/a</option>
                                <option value="IV/b" {{ old('pangkat_golongan', $a->pangkat_golongan) == 'IV/b' ? 'selected' : '' }}>IV/b</option>
                                <option value="IV/c" {{ old('pangkat_golongan', $a->pangkat_golongan) == 'IV/c' ? 'selected' : '' }}>IV/c</option>
                                <option value="IV/d" {{ old('pangkat_golongan', $a->pangkat_golongan) == 'IV/d' ? 'selected' : '' }}>IV/d</option>
                                <option value="IV/e" {{ old('pangkat_golongan', $a->pangkat_golongan) == 'IV/e' ? 'selected' : '' }}>IV/e</option>
                            @elseif (old('status', $a->status) == 'PPPK')
                                <option value="IX" {{ old('pangkat_golongan', $a->pangkat_golongan) == 'IX' ? 'selected' : '' }}>IX</option>
                                <option value="X" {{ old('pangkat_golongan', $a->pangkat_golongan) == 'X' ? 'selected' : '' }}>X</option>
                                <option value="XI" {{ old('pangkat_golongan', $a->pangkat_golongan) == 'XI' ? 'selected' : '' }}>XI</option>
                                <option value="XII" {{ old('pangkat_golongan', $a->pangkat_golongan) == 'XII' ? 'selected' : '' }}>XII</option>
                                <option value="XIII" {{ old('pangkat_golongan', $a->pangkat_golongan) == 'XIII' ? 'selected' : '' }}>XIII</option>
                                <option value="XIV" {{ old('pangkat_golongan', $a->pangkat_golongan) == 'XIV' ? 'selected' : '' }}>XIV</option>
                                <option value="XV" {{ old('pangkat_golongan', $a->pangkat_golongan) == 'XV' ? 'selected' : '' }}>XV</option>
                                <option value="XVI" {{ old('pangkat_golongan', $a->pangkat_golongan) == 'XVI' ? 'selected' : '' }}>XVI</option>
                                <option value="XVII" {{ old('pangkat_golongan', $a->pangkat_golongan) == 'XVII' ? 'selected' : '' }}>XVII</option>
                            @endif
                        </select>
                    </div>

                    <div class="form-group mb-3">
                        <label for="pendidikan">Pendidikan</label>
                        <input type="text" name="pendidikan" id="pendidikan" class="form-control" value="{{ old('pendidikan', $a->pendidikan) }}" required>
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
