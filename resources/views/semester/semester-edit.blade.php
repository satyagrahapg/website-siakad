<!-- Ubah Semester Modal for this specific semester -->
<div class="modal fade" id="editSemesterModal-{{ $semester->id }}" tabindex="-1" aria-labelledby="editSemesterModalLabel-{{ $semester->id }}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('semesters.update', $semester->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="modal-header">
                    <h5 class="modal-title" id="editSemesterModalLabel-{{ $semester->id }}">Ubah Semester</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <div class="modal-body">
                    <!-- Semester Input -->
                    <div class="mb-3">
                        <label for="semester{{ $semester->id }}" class="form-label">Semester</label>
                        <input type="text" class="form-control" id="semester{{ $semester->id }}" name="semester" value="{{ $semester->semester }}" required>
                    </div>

                    <!-- Tahun Ajaran Input -->
                    <div class="mb-3">
                        <label for="tahun_ajaran{{ $semester->id }}" class="form-label">Tahun Ajaran</label>
                        <input type="text" class="form-control" id="tahun_ajaran{{ $semester->id }}" name="tahun_ajaran" value="{{ $semester->tahun_ajaran }}" required>
                    </div>

                    <!-- Status Input -->
                    <div class="mb-3">
                        <label for="status{{ $semester->id }}" class="form-label">Status</label>
                        <select class="form-select" id="status{{ $semester->id }}" name="status" required>
                            <option value="1" {{ $semester->status == 1 ? 'selected' : '' }}>Aktif</option>
                            <option value="0" {{ $semester->status == 0 ? 'selected' : '' }}>Tidak Aktif</option>
                        </select>
                    </div>

                    <!-- Start Date Input -->
                    <div class="mb-3">
                        <label for="start{{ $semester->id }}" class="form-label">Tanggal Dimulai</label>
                        <input type="date" class="form-control" id="start{{ $semester->id }}" name="start" value="{{ $semester->start }}" required>
                    </div>

                    <!-- End Date Input -->
                    <div class="mb-3">
                        <label for="end{{ $semester->id }}" class="form-label">Tanggal Berakhir</label>
                        <input type="date" class="form-control" id="end{{ $semester->id }}" name="end" value="{{ $semester->end }}" required>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style='width: 5.5rem;'>Tutup</button>
                    <button type="submit" class="btn btn-primary" style='width: 5.5rem;'>Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
