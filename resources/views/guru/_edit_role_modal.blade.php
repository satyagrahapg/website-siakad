<!-- Generate User Modal for each Guru -->
<div class="modal fade" id="editRoleModal-{{ $guru->id }}" tabindex="-1" aria-labelledby="editRoleModalLabel-{{ $guru->id }}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editRoleModalLabel-{{ $guru->id }}">Ubah Hak Akses {{ $guru->nama }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('guru.editRole', $guru->id) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="username-{{ $guru->id }}" class="form-label">Nama Pengguna</label>
                        <input type="text" class="form-control" placeholder="{{ $guru->nip }}" disabled>
                    </div>
                    <div class="mb-3">
                        <label for="roles" class="form-label">Hak Akses</label>
                        <select class="role-multiple form-select" name="roles[]" multiple="multiple" required>
                            <option value="Guru" {{ in_array("Guru", $guru->user->getRoleNames()->toArray()) ? 'selected' : '' }}>Guru</option>
                            <option value="Wali Kelas" {{ in_array("Wali Kelas", $guru->user->getRoleNames()->toArray()) ? 'selected' : '' }}>Wali Kelas</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </form>
            </div>
        </div>
    </div>
</div>
