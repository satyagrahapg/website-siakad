<!-- Generate User Modal for each Guru -->
<div class="modal fade" id="generateUserModal-{{ $siswa->id }}" tabindex="-1" aria-labelledby="generateUserModalLabel-{{ $siswa->id }}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="generateUserModalLabel-{{ $siswa->id }}">Buat Akun untuk {{ $siswa->nama }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('siswa.generateUser', $siswa->id) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="email-{{ $siswa->id }}" class="form-label">Email</label>
                        <input type="text" class="form-control @error('email') is-invalid @enderror" id="email-{{ $siswa->id }}" name="email" placeholder="example@email.com" required value="{{ old('email') }}">
                        
                        @error('email')
                            <label for="" class="invalid-feedback">{{ $message }}</label>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="username-{{ $siswa->id }}" class="form-label">Nama Pengguna</label>
                        <input type="hidden" class="form-control " id="username-{{ $siswa->id }}" name="username" value="{{ $siswa->nisn }}" required>
                        <input type="text" class="form-control @error('username') is-invalid @enderror" placeholder="{{ $siswa->nisn }}" disabled>

                        @error('username')
                            <label for="" class="invalid-feedback">{{ $message }}</label>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="password-{{ $siswa->id }}" class="form-label">Kata Sandi</label>
                        <input type="text" class="form-control @error('password') is-invalid @enderror" id="password-{{ $siswa->id }}" name="password" value="{{ Str::random(6) }}" required minlength="6">

                        @error('password')
                            <label for="" class="invalid-feedback">{{ $message }}</label>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-primary">Buat</button>
                </form>
            </div>
        </div>
    </div>
</div>