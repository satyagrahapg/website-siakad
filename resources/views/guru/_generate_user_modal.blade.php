<!-- Generate User Modal for each Guru -->
<div class="modal fade" id="generateUserModal-{{ $guru->id }}" tabindex="-1" aria-labelledby="generateUserModalLabel-{{ $guru->id }}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="generateUserModalLabel-{{ $guru->id }}">Buat Akun untuk {{ $guru->nama }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('guru.generateUser', $guru->id) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="email-{{ $guru->id }}" class="form-label">Email</label>
                        <input type="text" class="form-control" id="email-{{ $guru->id }}" name="email" placeholder="example@email.com" required>
                    </div>
                    <div class="mb-3">
                        <label for="username-{{ $guru->id }}" class="form-label">Nama Pengguna</label>
                        <input type="hidden" class="form-control" id="username-{{ $guru->id }}" name="username" value="{{ $guru->nip }}" required>
                        <input type="text" class="form-control" placeholder="{{ $guru->nip }}" disabled>
                    </div>
                    <div class="mb-3">
                        <label for="password-{{ $guru->id }}" class="form-label">Kata Sandi</label>
                        <input type="text" class="form-control" id="password-{{ $guru->id }}" name="password" value="{{ Str::random(6) }}" required minlength="6">
                    </div>
                    <div class="mb-3">
                        <label for="roles" class="form-label">Hak Akses</label>
                        <select class="role-multiple form-select" name="roles[]" multiple="multiple" required>
                            @role('Super Admin')
                            <option value="Super Admin">Super Admin</option>
                            <option value="Admin">Admin</option>
                            @endrole
                            <option value="Guru" selected>Guru</option>
                            <option value="Wali Kelas">Wali Kelas</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Buat</button>
                </form>
            </div>
        </div>
    </div>
</div>