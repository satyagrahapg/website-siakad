@foreach ($tendik as $a)
    <!-- Buat Modal for each Admin -->
    <div class="modal fade" id="generateUserModal-{{ $a->id }}" tabindex="-1"
        aria-labelledby="generateUserModalLabel-{{ $a->id }}" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="generateUserModalLabel-{{ $a->id }}">Buat Akun untuk
                        {{ $a->nama }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('tendik.generateUser', $a->id) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="email-{{ $a->id }}" class="form-label">Email</label>
                            <input type="text" class="form-control" id="email-{{ $a->id }}" name="email"
                                placeholder="example@email.com" required>
                        </div>
                        <div class="mb-3">
                            <label for="username-{{ $a->id }}" class="form-label">Nama Pengguna</label>
                            <input type="hidden" class="form-control" id="username-{{ $a->id }}" name="username"
                                value="{{ $a->nip }}" required>
                            <input type="text" class="form-control" placeholder="{{ $a->nip }}" disabled>
                        </div>
                        <div class="mb-3">
                            <label for="password-{{ $a->id }}" class="form-label">Kata Sandi</label>
                            <input type="text" class="form-control" id="password-{{ $a->id }}" name="password" value="{{ Str::random(6) }}" required minlength="6">
                        </div>
                        <div class="mb-3">
                            <label for="roles" class="form-label">Hak Akses</label>
                            <select class="role-multiple form-select" name="roles[]" multiple="multiple" required>
                                <option value="Super Admin">Super Admin</option>
                                <option value="Admin">Admin</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Buat</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endforeach
