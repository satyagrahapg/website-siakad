@extends('layout.layout')

@push('style')
    <style>
        p {
            margin-bottom: 5px;
        }

        strong {
            display: inline-block;
            width: 200px; /* Tentukan lebar label */
        }

        .image-upload-container {
            position: relative;
            display: inline-block;
        }
        
        .image-hover {
            width: 180px; /* Sesuaikan dengan ukuran gambar */
            object-fit: cover;
            transition: opacity 0.3s;
        }
        
        .upload-form {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            opacity: 0; /* Menyembunyikan form upload secara default */
            pointer-events: none;
            transition: opacity 0.3s;
        }
        
        .upload-input {
            display: none; /* Menyembunyikan input file */
        }
        
        .upload-button {
            padding: 10px 20px;
            background-color: #37B7C3;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            display: inline-block;
        }
        
        .upload-button:hover {
            background-color: #37B7C3;
        }
        
        .image-upload-container:hover .upload-form {
            opacity: 1; /* Menampilkan form upload saat gambar di-hover */
            pointer-events: all;
        }
    </style>
@endpush

@section('content')
    <div class="container" style="margin: 20px">
        <div class="row">
            <div class="col-2">
                
                <div class="image-upload-container">
                    @if (auth()->user()->picture)
                        <img src="data:image/jpeg;base64,{{ base64_encode(auth()->user()->picture) }}" class="image-hover" alt="Profile Picture">
                    @else
                        <img src="{{ asset('style/assets/default_picture.jpg') }}" class="image-hover" alt="Default Profile Picture">
                    @endif
                    
                    <form action="{{ route('update_picture') }}" method="POST" enctype="multipart/form-data" class="upload-form">
                        @csrf
                        <input type="file" name="image" accept="image/png, image/jpeg" id="imageInput" class="upload-input" />
                        <button type="button" class="upload-button" id="chooseButton">Pilih Gambar</button>
                        <button type="submit" class="upload-button" id="uploadButton" style="display:none;">Unggah Gambar</button>
                    </form>
                </div>

                @role('Super Admin')
                    <h4 style="margin-top: 10px">Super Admin</h4>    
                @endrole

                @role('Guru')
                    <h4 style="margin-top: 10px">Guru</h4>
                @endrole

                @role('Wali Kelas')
                    <h4 style="margin-top: 10px">Wali Kelas</h4>
                @endrole

                @role('Admin')
                    <h4 style="margin-top: 10px">Tenaga Kependidikan</h4>    
                @endrole

                @role('Siswa')
                    <h4 style="margin-top: 10px">Peserta Didik</h4>
                @endrole
            </div>
            <div class="col">
                <p><strong>Nama Pengguna</strong> : {{ auth()->user()->username }}</p>
                <p><strong>Email</strong> : {{ auth()->user()->email }}</p>
                <form action="{{ route('update_password') }}" method="POST" style="display: flex; align-items: center;">
                    @csrf
                    <p><strong>Kata Sandi</strong> : 
                        <input type="text" name="new_password" style="height: 25px; border-radius: 5px; width: 250px;" required placeholder="Masukkan kata sandi baru" minlength="6"/>
                        <button type="submit" style="height: 25px; background-color: #37B7C3; color: white; border: none; border-radius: 5px; cursor: pointer;">Ubah</button>
                    </p>
                </form>
                <p><strong>Nama</strong> : {{ auth()->user()->name }}</p>
                @if (isset($data->gelar) && !empty($data->gelar))
                    <p><strong>Nama dan Gelar</strong> : {{ trim($data->gelar_depan." ".$data->nama."".$data->gelar_belakang) }}</p>
                @endif
                @role('Super Admin|Admin|Guru|Wali Kelas')
                    <p><strong>NIP / Kode Pegawai </strong> : {{ $data->nip ?? '-' }}</p>
                    <p><strong>Jabatan</strong> : {{ $data->jabatan ?? '-' }}</p>
                    <p><strong>Status</strong> : {{ $data->status ?? '-' }}</p>
                    <p><strong>Pangkat Golongan</strong> : {{ $data->pangkat_golongan ?? '-' }}</p>
                    <p><strong>Pendidikan</strong> : {{ $data->pendidikan ?? '-' }}</p>
                    <p><strong>Tempat Lahir</strong> : {{ $data->tempat_lahir ?? '-' }}</p>
                    <p><strong>Tanggal Lahir</strong> : {{ $data->tanggal_lahir ?? '-' }}</p>
                    <p><strong>Jenis Kelamin</strong> : {{ $data->jenis_kelamin ?? '-' }}</p>
                    <p><strong>Agama</strong> : {{ $data->agama ?? '-' }}</p>
                    <p><strong>Alamat</strong> : {{ $data->alamat ?? '-' }}</p>
                @endrole
            
                @role('Siswa')
                    <!-- <p><strong>NIS</strong> : {{ $data->nis ?? '-' }}</p> -->
                    <p><strong>NISN</strong> : {{ $data->nisn ?? '-' }}</p>
                    <p><strong>Tempat Lahir</strong> : {{ $data->tempat_lahir ?? '-' }}</p>
                    <p><strong>Tanggal Lahir</strong> : {{ $data->tanggal_lahir ?? '-' }}</p>
                    <p><strong>Jenis Kelamin</strong> : {{ $data->jenis_kelamin ?? '-' }}</p>
                    <p><strong>Agama</strong> : {{ $data->agama ?? '-' }}</p>
                    <p><strong>Status Keluarga</strong> : {{ $data->status_keluarga}}</p>
                    <p><strong>Anak Ke</strong> : {{ $data->anak_ke ?? '-' }}</p>
                    <p><strong>Alamat</strong> : {{ $data->alamat ?? '-' }}</p>
                    <p><strong>Telepon</strong> : {{ $data->telepon ?? '-' }}</p>
                    <p><strong>Asal Sekolah</strong> : {{ $data->asal_sekolah ?? '-' }}</p>
                    <p><strong>Tanggal Diterima</strong> : {{ $data->tanggal_diterima ?? '-' }}</p>
                    <p><strong>Jalur Penerimaan</strong> : {{ $data->jalur_penerimaan ?? '-' }}</p>
                    <p><strong>Nama Ayah</strong> : {{ $data->nama_ayah ?? '-' }}</p>
                    <p><strong>Pekerjaan Ayah</strong> : {{ $data->pekerjaan_ayah ?? '-' }}</p>
                    <p><strong>Nama Ibu</strong> : {{ $data->nama_ibu ?? '-' }}</p>
                    <p><strong>Pekerjaan Ibu</strong> : {{ $data->pekerjaan_ibu ?? '-' }}</p>
                    <!-- <p><strong>Alamat Ortu</strong> : {{ $data->alamat_ortu ?? '-' }}</p> -->
                    <!-- <p><strong>No Telepon Ortu</strong> : {{ $data->no_telp_ortu ?? '-' }}</p> -->
                    <p><strong>Nama Wali</strong> : {{ $data->nama_wali ?? '-' }}</p>
                    <!-- <p><strong>Alamat Wali</strong> : {{ $data->alamat_wali ?? '-' }}</p> -->
                    <p><strong>Pekerjaan Wali</strong> : {{ $data->pekerjaan_wali ?? '-' }}</p>
                    <p><strong>Angkatan</strong> : {{ $data->angkatan ?? '-' }}</p>
                @endrole
            </div>
        </div>
    </div>    
@endsection

@push('script')
    <script>
      // Ketika tombol "Pilih Gambar" diklik, buka dialog file explorer
document.getElementById('chooseButton').addEventListener('click', function() {
    document.getElementById('imageInput').click(); // Trigger klik pada input file
});

// Menambahkan event listener untuk perubahan pada input file
document.getElementById('imageInput').addEventListener('change', function() {
    var file = this.files[0]; // Mendapatkan file yang dipilih

    // Cek apakah file yang dipilih adalah gambar PNG atau JPG
    if (file && (file.type === 'image/png' || file.type === 'image/jpeg')) {
        // Jika file valid (PNG/JPG), ganti tombol "Pilih Gambar" menjadi "Unggah Gambar"
        document.getElementById('chooseButton').style.display = 'none'; // Sembunyikan tombol Pilih Gambar
        document.getElementById('uploadButton').style.display = 'inline-block'; // Tampilkan tombol Unggah
    } else {
        // Jika file tidak valid, tampilkan alert dan reset form
        alert('File harus berformat PNG atau JPEG');
        document.getElementById('imageInput').value = ''; // Reset input file
    }
});


    </script>
@endpush