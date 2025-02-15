<!-- Ubah Account Modal -->
<div class="modal fade" id="editAccountModal-{{ $account->id }}" tabindex="-1" aria-labelledby="editAccountModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editAccountModalLabel">Ubah data {{ $account->nama }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('account.update', $account->id) }}" method="POST">
                @csrf
                @method('PUT') <!-- Use PUT here to match REST conventions for updates -->
                
                <div class="modal-body">
                    <!-- Name Input -->
                    <div class="form-group mb-3">
                        <label for="name">Nama</label>
                        <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $account->name) }}" required>
                    </div>

                    <!-- Email Input -->
                    <div class="form-group mb-3">
                        <label for="email">Email</label>
                        <input type="text" name="email" id="email" class="form-control" value="{{ old('email', $account->email) }}" required>
                    </div>

                    <!-- Username Input -->
                    <div class="form-group mb-3">
                        <label for="username">Nama Pengguna</label>
                        <input type="text" name="username" id="username" class="form-control" value="{{ old('username', $account->username) }}" required>
                    </div>

                    <!-- Password Input -->
                    <div class="form-group mb-3">
                        <label for="password">Kata Sandi Baru</label>
                        <input type="text" name="password" id="password" class="form-control" placeholder="Masukkan kata sandi baru">
                    </div>

                    <!-- Role Selection Dropdown -->
                    <div class="form-group mb-3">
                        <label for="roles">Hak Akses</label>
                        <select class="role-multiple form-select" name="roles[]" multiple="multiple">
                            @foreach(["Super Admin", "Admin", "Guru", "Wali Kelas", "Siswa"] as $role)
                                <option value="{{ $role }}" {{ in_array($role, $account->getRoleNames()->toArray()) ? 'selected' : '' }}>{{ $role }}</option>
                            @endforeach
                        </select>
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
