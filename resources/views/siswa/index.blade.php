@extends('layout.layout')

@push('style')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.bootstrap5.css">
@endpush

@section('content')
<div class="container-fluid mt-3">
    <div class="card mb-3 border-0 shadow-sm" style="background-color:#f2f2f2;">
        <div class="card-body" style="background-color: #37B7C3; border-radius: 8px">
            <h2 class="m-0" style="color: #EBF4F6">Peserta Didik</h2>
        </div>
    </div>

    <!-- Import Modal -->
    <div class="modal fade" data-bs-backdrop="static" tabindex="-1" aria-hidden="true" id="excelModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Impor Data Siswa dari Excel</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('siswa.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="m-3">
                        <input type="file" name="file" class="form-control" accept=".xlsx" required>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-success">Impor</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    
    <!-- Import Button -->
        <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#excelModal" style="width: 6rem">Impor</button>
    <!-- Ekspor Button -->
    <a target="_blank" href="{{ route('siswa.export') }}" class="btn btn-secondary mb-3 px-3" style="width: 6rem">Ekspor</a>
    <!-- Tambah Button -->
    <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#createSiswaModal" style="width: 6rem">Tambah</button>

    
    
    {{-- toggle to enable "Edit" and "Delete" buttons --}} 
    <div class="form-check form-switch">
        <input class="form-check-input" type="checkbox" role="switch" id="flexSwitchCheckDefault" checked>  
        <label class="form-check-label" for="flexSwitchCheckDefault">Mode Edit</label>
    </div>


    <!-- Data Table -->
    <table id="example" class="table table-striped" style="width:100%">
        <thead>
            <tr>
                <th class="text-start">No</th>
                <th class="text-start">Nama</th>
                <th class="text-start">NISN</th>
                <th class="text-start">Jenis Kelamin</th>
                <th class="text-start">Angkatan</th>
                <th class="text-start">Agama</th>
                <!-- <th>Alamat</th> -->
                <th>Aksi</th>
                <th>Akun</th>
            </tr>
        </thead>
        <tbody>
            @foreach($siswas as $siswa)
            <tr>
                <td class="text-start">{{ $loop->iteration }}</td>
                <td class="text-start">{{ $siswa->nama }}</td>
                <td class="text-start">{{ $siswa->nisn }}</td>
                <!-- <td class="text-start">{{ $siswa->nis }}</td> -->
                <td class="text-start">{{ $siswa->jenis_kelamin }}</td>
                <td class="text-start">{{ $siswa->angkatan }}</td>
                <td class="text-start">{{ $siswa->agama }}</td>
                <td>
                    <!-- View Button to trigger modal -->
                    <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#viewSiswaModal-{{ $siswa->id }}" >
                        <i class="fa-solid fa-eye"></i>
                    </button>
                    
                    <!-- Edit Button to trigger modal -->
                    <button type="button" class="btn btn-warning controlled" data-bs-toggle="modal" data-bs-target="#editSiswaModal-{{ $siswa->id }}" >
                        <i class="fa-solid fa-edit"></i>
                    </button>

                    <!-- Delete Form -->
                    <form action="{{ route('siswa.delete', $siswa->id) }}" method="POST" class="d-inline delete-form" id="deleteForm-{{ $siswa->id }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger delete-button deleteAlert controlled" data-siswa-id="{{ $siswa->id }}" aria-label="Hapus Siswa" >
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </form>
                </td>
                <td>
                    <!-- Generate Single User Form -->
                    @if(empty($siswa->id_user))
                        <!-- Button to open the generate user modal -->
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#generateUserModal-{{ $siswa->id }}">
                                <i class="fa-solid fa-plus"></i>
                            </button>
                            @include('siswa._generate_user_modal')
                    @else
                        @role('Super Admin')
                            {{-- <span>User ID: {{ $siswa->id_user }}</span> --}}
                            <a href="{{ route('account.index') }}">Lihat</a>
                        @endrole
                        @role('Admin')
                            <span>Sudah Ada</span>
                        @endrole
                    @endif
                </td>

                <!-- Edit Modal for each Siswa -->
                <div class="modal fade" id="editSiswaModal-{{ $siswa->id }}" tabindex="-1" aria-labelledby="editSiswaModalLabel-{{ $siswa->id }}" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-scrollable modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="editSiswaModalLabel-{{ $siswa->id }}">Ubah Data Siswa</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                                    <!-- <span aria-hidden="true">&times;</span> -->
                                </button>
                            </div>
                            <form action="{{ route('siswa.update', $siswa->id) }}" method="POST" class="m-0">
                                @csrf
                                @method('PUT')
                                <div class="modal-body">
                                    <div class="form-group mb-3">
                                        <label for="nama">Nama</label>
                                        <input type="text" name="nama" id="nama" class="form-control" value="{{ old('nama', $siswa->nama) }}">
                                    </div>
                                    <!-- {{-- <div class="form-group mb-3">
                                        <label for="nis">NIS</label>
                                        <input type="text" name="nis" id="nis" class="form-control" value="{{ old('nis', $siswa->nis) }}">
                                    </div> --}} -->
                                    <div class="form-group mb-3">
                                        <label for="nisn">NISN</label>
                                        <input type="text" name="nisn" id="nisn" class="form-control" value="{{ old('nisn', $siswa->nisn) }}">
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="tempat_lahir">Tempat Lahir</label>
                                        <input type="text" name="tempat_lahir" id="tempat_lahir" class="form-control" value="{{ old('tempat_lahir', $siswa->tempat_lahir) }}">
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="tanggal_lahir">Tanggal Lahir</label>
                                        <input type="date" name="tanggal_lahir" id="tanggal_lahir" class="form-control" value="{{ old('tanggal_lahir', $siswa->tanggal_lahir) }}">
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="jenis_kelamin">Jenis Kelamin</label>
                                        <select name="jenis_kelamin" id="jenis_kelamin" class="form-control">
                                            <option value="Laki-laki" {{ $siswa->jenis_kelamin == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                                            <option value="Perempuan" {{ $siswa->jenis_kelamin == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                                        </select>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="agama">Agama</label>
                                        <input type="text" name="agama" id="agama" class="form-control" value="{{ old('agama', $siswa->agama) }}">
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="status_keluarga">Status Keluarga</label>
                                        <input type="text" name="status_keluarga" id="status_keluarga" class="form-control" value="{{ old('status_keluarga', $siswa->status_keluarga) }}">
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="anak_ke">Anak Ke</label>
                                        <input type="number" name="anak_ke" id="anak_ke" class="form-control" value="{{ old('anak_ke', $siswa->anak_ke) }}">
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="alamat">Alamat</label>
                                        <textarea name="alamat" id="alamat" class="form-control">{{ old('alamat', $siswa->alamat) }}</textarea>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="telepon">Telepon </label>
                                        <input type="text" name="telepon" id="telepon" class="form-control" value="{{ old('telepon', $siswa->telepon) }}">
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="asal_sekolah">Asal Sekolah</label>
                                        <input type="text" name="asal_sekolah" id="asal_sekolah" class="form-control" value="{{ old('asal_sekolah', $siswa->asal_sekolah) }}">
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="tanggal_diterima">Tanggal Diterima</label>
                                        <input type="date" name="tanggal_diterima" id="tanggal_diterima" class="form-control" value="{{ old('tanggal_diterima', $siswa->tanggal_diterima) }}">
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="jalur_penerimaan">Jalur Penerimaan</label>
                                        <input type="text" name="jalur_penerimaan" id="jalur_penerimaan" class="form-control" value="{{ old('jalur_penerimaan', $siswa->jalur_penerimaan) }}">
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="nama_ayah">Nama Ayah</label>
                                        <input type="text" name="nama_ayah" id="nama_ayah" class="form-control" value="{{ old('nama_ayah', $siswa->nama_ayah) }}">
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="pekerjaan_ayah">Pekerjaan Ayah</label>
                                        <input type="text" name="pekerjaan_ayah" id="pekerjaan_ayah" class="form-control" value="{{ old('pekerjaan_ayah', $siswa->pekerjaan_ayah) }}">
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="nama_ibu">Nama Ibu</label>
                                        <input type="text" name="nama_ibu" id="nama_ibu" class="form-control" value="{{ old('nama_ibu', $siswa->nama_ibu) }}">
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="pekerjaan_ibu">Pekerjaan Ibu</label>
                                        <input type="text" name="pekerjaan_ibu" id="pekerjaan_ibu" class="form-control" value="{{ old('pekerjaan_ibu', $siswa->pekerjaan_ibu) }}">
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="nama_wali">Nama Wali</label>
                                        <input type="text" name="nama_wali" id="nama_wali" class="form-control" value="{{ old('nama_wali', $siswa->nama_wali) }}">
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="pekerjaan_wali">Pekerjaan Wali</label>
                                        <input type="text" name="pekerjaan_wali" id="pekerjaan_wali" class="form-control" value="{{ old('pekerjaan_wali', $siswa->pekerjaan_wali) }}">
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="angkatan">Angkatan</label>
                                        <input type="number" name="angkatan" id="angkatan" class="form-control" value="{{ old('angkatan', $siswa->angkatan) }}">
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

                <!-- View Modal for each Siswa -->
                <div class="modal fade" id="viewSiswaModal-{{ $siswa->id }}" tabindex="-1" aria-labelledby="viewSiswaModalLabel-{{ $siswa->id }}" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-scrollable modal-lg">
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
                                    {{-- <div class="form-group mb-3">
                                        <label for="nis">NIS</label>
                                        <input type="text" name="nis" id="nis" class="form-control" value="{{ old('nis', $siswa->nis) }}">
                                    </div> --}}
                                    <div class="form-group mb-3">
                                        <label for="nisn">NISN</label>
                                        <input type="text" name="nisn" id="nisn" class="form-control" value="{{ old('nisn', $siswa->nisn) }}" disabled>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="tempat_lahir">Tempat Lahir</label>
                                        <input type="text" name="tempat_lahir" id="tempat_lahir" class="form-control" value="{{ old('tempat_lahir', $siswa->tempat_lahir) }}" disabled>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="tanggal_lahir">Tanggal Lahir</label>
                                        <input type="date" name="tanggal_lahir" id="tanggal_lahir" class="form-control" value="{{ old('tanggal_lahir', $siswa->tanggal_lahir) }}" disabled>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="jenis_kelamin">Jenis Kelamin</label>
                                        <select name="jenis_kelamin" id="jenis_kelamin" class="form-control" disabled>
                                            <option value="Laki-laki" {{ $siswa->jenis_kelamin == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                                            <option value="Perempuan" {{ $siswa->jenis_kelamin == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                                        </select>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="agama">Agama</label>
                                        <input type="text" name="agama" id="agama" class="form-control" value="{{ old('agama', $siswa->agama) }}" disabled>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="status_keluarga">Status Keluarga</label>
                                        <input type="text" name="status_keluarga" id="status_keluarga" class="form-control" value="{{ old('status_keluarga', $siswa->status_keluarga) }}" disabled>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="anak_ke">Anak Ke</label>
                                        <input type="number" name="anak_ke" id="anak_ke" class="form-control" value="{{ old('anak_ke', $siswa->anak_ke) }}" disabled>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="alamat">Alamat</label>
                                        <textarea name="alamat" id="alamat" class="form-control" disabled>{{ old('alamat', $siswa->alamat) }}</textarea>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="telepon">No Telepon </label>
                                        <input type="text" name="telepon" id="telepon" class="form-control" value="{{ old('telepon', $siswa->telepon) }}" disabled>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="asal_sekolah">Asal Sekolah</label>
                                        <input type="text" name="asal_sekolah" id="asal_sekolah" class="form-control" value="{{ old('asal_sekolah', $siswa->asal_sekolah) }}" disabled>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="tanggal_diterima">Tanggal Diterima</label>
                                        <input type="date" name="tanggal_diterima" id="tanggal_diterima" class="form-control" value="{{ old('tanggal_diterima', $siswa->tanggal_diterima) }}" disabled>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="jalur_penerimaan">Jalur Penerimaan</label>
                                        <input type="text" name="jalur_penerimaan" id="jalur_penerimaan" class="form-control" value="{{ old('jalur_penerimaan', $siswa->jalur_penerimaan) }}" disabled>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="nama_ayah">Nama Ayah</label>
                                        <input type="text" name="nama_ayah" id="nama_ayah" class="form-control" value="{{ old('nama_ayah', $siswa->nama_ayah) }}" disabled>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="pekerjaan_ayah">Pekerjaan Ayah</label>
                                        <input type="text" name="pekerjaan_ayah" id="pekerjaan_ayah" class="form-control" value="{{ old('pekerjaan_ayah', $siswa->pekerjaan_ayah) }}" disabled>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="nama_ibu">Nama Ibu</label>
                                        <input type="text" name="nama_ibu" id="nama_ibu" class="form-control" value="{{ old('nama_ibu', $siswa->nama_ibu) }}" disabled>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="pekerjaan_ibu">Pekerjaan Ibu</label>
                                        <input type="text" name="pekerjaan_ibu" id="pekerjaan_ibu" class="form-control" value="{{ old('pekerjaan_ibu', $siswa->pekerjaan_ibu) }}" disabled>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="nama_wali">Nama Wali</label>
                                        <input type="text" name="nama_wali" id="nama_wali" class="form-control" value="{{ old('nama_wali', $siswa->nama_wali) }}" disabled>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="pekerjaan_wali">Pekerjaan Wali</label>
                                        <input type="text" name="pekerjaan_wali" id="pekerjaan_wali" class="form-control" value="{{ old('pekerjaan_wali', $siswa->pekerjaan_wali) }}" disabled>
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
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Create Modal -->
    <!-- Edit Modal for each Siswa -->
    <div class="">
    <div class="modal fade" id="createSiswaModal" tabindex="-1" aria-labelledby="createiswaModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
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
                            <input type="text" name="nama" id="nama_create" class="form-control">
                        </div>
                        {{-- <div class="form-group mb-3">) }}
                            <label for="nis">NIS</label>
                            <input type="text" name="nis" id="nis" class="form-control" value="{{ old('nis', $siswa->nis) }}">
                        </div> --}}
                        <div class="form-group mb-3">
                            <label for="nisn_create">NISN</label>
                            <input type="text" name="nisn" id="nisn_create" class="form-control">
                        </div>
                        <div class="form-group mb-3">
                            <label for="tempat_lahir_create">Tempat Lahir</label>
                            <input type="text" name="tempat_lahir" id="tempat_lahir_create" class="form-control" >
                        </div>
                        <div class="form-group mb-3">
                            <label for="tanggal_lahir_create">Tanggal Lahir</label>
                            <input type="date" name="tanggal_lahir" id="tanggal_lahir_create" class="form-control" >
                        </div>
                        <div class="form-group mb-3">
                            <label for="jenis_kelamin_create">Jenis Kelamin</label>
                            <select name="jenis_kelamin" id="jenis_kelamin_create" class="form-control">
                                <option value="Laki-laki">Laki-laki</option>
                                <option value="Perempuan" >Perempuan</option>
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label for="agama_create">Agama</label>
                            <input type="text" name="agama" id="agama_create" class="form-control">
                        </div>
                        <div class="form-group mb-3">
                            <label for="status_keluarga_create">Status Keluarga</label>
                            <input type="text" name="status_keluarga" id="status_keluarga_create" class="form-control">
                        </div>
                        <div class="form-group mb-3">
                            <label for="anak_ke_create">Anak Ke</label>
                            <input type="number" name="anak_ke" id="anak_ke_create" class="form-control" >
                        </div>
                        <div class="form-group mb-3">
                            <label for="alamat_create">Alamat</label>
                            <textarea name="alamat_lengkap" id="alamat_create" class="form-control"></textarea>
                        </div>
                        <div class="form-group mb-3">
                            <label for="telepon_create">No Telepon </label>
                            <input type="text" name="no_telepon" id="telepon_create" class="form-control">
                        </div>
                        <div class="form-group mb-3">
                            <label for="asal_sekolah_create">Asal Sekolah</label>
                            <input type="text" name="asal_sekolah" id="asal_sekolah_create" class="form-control">
                        </div>
                        <div class="form-group mb-3">
                            <label for="tanggal_diterima_create">Tanggal Diterima</label>
                            <input type="date" name="tanggal_diterima" id="tanggal_diterima_create" class="form-control" >
                        </div>
                        <div class="form-group mb-3">
                            <label for="jalur_penerimaan_create">Jalur Penerimaan</label>
                            <input type="text" name="jalur_penerimaan" id="jalur_penerimaan_create" class="form-control" >
                        </div>
                        <div class="form-group mb-3">
                            <label for="nama_ayah_create">Nama Ayah</label>
                            <input type="text" name="nama_ayah" id="nama_ayah_create" class="form-control" >
                        </div>
                        <div class="form-group mb-3">
                            <label for="pekerjaan_ayah_create">Pekerjaan Ayah</label>
                            <input type="text" name="pekerjaan_ayah" id="pekerjaan_ayah_create" class="form-control" >
                        </div>
                        <div class="form-group mb-3">
                            <label for="nama_ibu_create">Nama Ibu</label>
                            <input type="text" name="nama_ibu" id="nama_ibu_create" class="form-control" >
                        </div>
                        <div class="form-group mb-3">
                            <label for="pekerjaan_ibu_create">Pekerjaan Ibu</label>
                            <input type="text" name="pekerjaan_ibu" id="pekerjaan_ibu_create" class="form-control" >
                        </div>
                        <div class="form-group mb-3">
                            <label for="nama_wali_create">Nama Wali</label>
                            <input type="text" name="nama_wali" id="nama_wali_create" class="form-control" >
                        </div>
                        <div class="form-group mb-3">
                            <label for="pekerjaan_wali_create">Pekerjaan Wali</label>
                            <input type="text" name="pekerjaan_wali" id="pekerjaan_wali_create" class="form-control" >
                        </div>
                        <div class="form-group mb-3">
                            <label for="angkatan_create">Angkatan</label>
                            <input type="number" name="angkatan" id="angkatan_create" class="form-control" >
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
    </div>
</div>
@endsection

@push('script')
    
@if(session('success'))
<!-- success alert -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
            title: "Berhasil!",
            text: "{{ session('success') }}",
            icon: "success",
            timer: 1500, // Waktu dalam milidetik (3000 = 3 detik)
            showConfirmButton: false
        });
    });
</script>
@endif
<script src="https://code.jquery.com/jquery-3.7.1.js"></script>
{{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script> --}}
<script src="https://cdn.datatables.net/2.1.8/js/dataTables.js"></script>
<script src="https://cdn.datatables.net/2.1.8/js/dataTables.bootstrap5.js"></script>
@if($errors->hasAny(['username','password','email']))
    <!-- success alert -->
    <script>
        Swal.fire({
            title: "Gagal! Mohon periksa kembali data yang digunakan",
            text: "Mohon periksa kembali data yang digunakan",
            icon: "error",
            timer: 5000, // Waktu dalam milidetik (3000 = 3 detik)
            showConfirmButton: false
        });
    </script>
@endif
<script>
    $(document).ready(function() {
        // Cek apakah DataTable sudah diinisialisasi
        if ($.fn.DataTable.isDataTable('#example')) {
            $('#example').DataTable().destroy(); // Hancurkan DataTable yang ada
        }

        // Inisialisasi DataTable dengan opsi
        $('#example').DataTable({
            language: {
                url: "{{ asset('style/js/bahasa.json') }}" // Ganti dengan path ke file bahasa Anda
            }
        });

        // Enable Edit and Delete buttons when toggle is checked
            $('#flexSwitchCheckDefault').on('change', function() {
                const isEditMode = this.checked;

                // Enable or disable all controlled buttons
                document.querySelectorAll('.controlled').forEach(button => {
                    button.disabled = !isEditMode;
                });
            });
    });
</script>
<script>
    document.querySelectorAll('.deleteAlert').forEach(function(button, index) {
        button.addEventListener('click', function(event) {
            event.preventDefault();
            Swal.fire({
                title: "Apakah Anda Yakin?",
                text: "Data Akan Dihapus Permanen dari Basis Data!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Ya, Hapus!",
                cancelButtonText: "Batal"
            }).then((result) => {
                // Jika konfirmasi "Ya, Hapus!" diklik
                if (result.isConfirmed) {
                    // Mengirim formulir untuk menghapus data
                    event.target.closest('form').submit();
                }
            });
        });
    });
</script>

@endpush