<div class="card">
    <div class="card-header">
        <h2 class="card-title">📥 Import Data Siswa (Excel)</h2>
        <a href="/admin/siswa" class="btn btn-ghost btn-sm">← Kembali</a>
    </div>

    <div class="card-body">
        <div class="alert alert-success" style="margin-bottom: 1.5rem;">
            <strong>💡 Panduan Import:</strong>
            <ol class="mt-2" style="margin-left: 1.5rem; line-height: 1.8;">
                <li>Pastikan Anda telah mengunduh <strong>Template Excel</strong> di bawah ini.</li>
                <li>Isi data siswa mulai dari <strong>Baris ke-2</strong> sesuai dengan format kolom yang disediakan.</li>
                <li>Kolom <strong>NISN</strong> digunakan sebagai referensi utama. Jika NISN sudah ada di database, data akan <strong>diperbarui (update)</strong>, jika belum ada akan <strong>ditambahkan baru (insert)</strong>.</li>
                <li>Pastikan format Tanggal Lahir adalah <code>YYYY-MM-DD</code> (contoh: <code>2005-08-15</code>).</li>
                <li>Simpan file dalam format <code>.xls</code> atau <code>.xlsx</code>.</li>
            </ol>
        </div>

        <div style="display: flex; gap: 1rem; margin-bottom: 2rem;">
            <a href="/admin/siswa/template" class="btn btn-secondary">
                📄 Download Template Excel
            </a>
        </div>

        <hr style="border: 0; border-top: 1px solid var(--clr-border); margin: 2rem 0;">

        <form method="POST" action="/admin/siswa/import" enctype="multipart/form-data">
            <?= csrf_field() ?>
            
            <div class="form-group" style="max-width: 500px;">
                <label class="form-label" for="excel_file">Upload File Excel <span class="required">*</span></label>
                <input type="file" id="excel_file" name="excel_file" class="form-input" accept=".xls,.xlsx" required>
            </div>

            <div class="form-actions" style="justify-content: flex-start; margin-top: 1.5rem;">
                <button type="submit" class="btn btn-primary">
                    🚀 Proses Import Data
                </button>
            </div>
        </form>
    </div>
</div>
