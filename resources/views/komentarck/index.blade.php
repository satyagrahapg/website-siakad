@extends('layout.layout')

@section('content')
<div class="container-fluid mt-3">
    <div class="card mb-3 border-0 shadow-sm" style="background-color:#f2f2f2;">
        <div class="card-body" style="background-color: #37B7C3; border-radius: 8px">
            <h2 class="m-0" style="color: #EBF4F6">Komentar {{ $mapel->nama }} Kelas {{ $mapel->kelas }}</h2>
        </div>
    </div>

    <!-- Form to update comments -->
    <form action="{{ route('komentar.update', $mapelId) }}" method="POST">
        @csrf

        <div class="form-group mb-3">
            <label for="komentar_tengah_semester">Komentar Tengah Semester</label>
            <textarea name="komentar_tengah_semester" id="komentar_tengah_semester" rows="4" class="form-control" required>{{ old('komentar_tengah_semester', $komentarCK->komentar_tengah_semester) }}</textarea>
            @error('komentar_tengah_semester')
            <div class="alert alert-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group mb-3">
            <label for="komentar_akhir_semester">Komentar Akhir Semester</label>
            <textarea name="komentar_akhir_semester" id="komentar_akhir_semester" rows="4" class="form-control" required>{{ old('komentar_akhir_semester', $komentarCK->komentar_akhir_semester) }}</textarea>
            @error('komentar_akhir_semester')
            <div class="alert alert-danger">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary">Update Komentar</button>
    </form>
</div>

<!-- success alert -->
@if(session('success'))
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

<!-- error alert -->
@if(session('error'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
            title: "Gagal!",
            text: "{{ session('error') }}",
            icon: "error",
            timer: 1500, // Waktu dalam milidetik (1500 = 1.5 detik)
            showConfirmButton: false
        });
    });
</script>
@endif
@endsection