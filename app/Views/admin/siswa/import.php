<div class="bg-surface border border-border rounded-2xl overflow-hidden shadow-sm animate-fade-in">
    <div class="flex items-center justify-between p-6 border-b border-border">
        <h2 class="text-lg font-bold">📥 Import Data Siswa (Excel)</h2>
        <a href="/admin/siswa" class="text-text-muted hover:text-text text-sm font-bold transition-colors">← Kembali</a>
    </div>

    <div class="p-6">
        <div class="bg-indigo-500/10 border border-indigo-500/30 text-indigo-300 p-6 rounded-2xl mb-8">
            <strong class="text-indigo-200 block mb-3">💡 Panduan Import:</strong>
            <ol class="flex flex-col gap-2 list-decimal list-inside text-sm leading-relaxed opacity-90">
                <li>Pastikan Anda telah mengunduh <strong class="text-white">Template Excel</strong> di bawah ini.</li>
                <li>Isi data siswa mulai dari <strong class="text-white">Baris ke-2</strong> sesuai dengan format kolom yang disediakan.</li>
                <li>Kolom <strong class="text-white">NISN</strong> digunakan sebagai referensi utama. Jika NISN sudah ada di database, data akan diperbarui, jika belum ada akan ditambahkan baru.</li>
                <li>Pastikan format Tanggal Lahir adalah <code class="bg-white/10 px-1.5 py-0.5 rounded text-white">YYYY-MM-DD</code>.</li>
                <li>Simpan file dalam format <code class="bg-white/10 px-1.5 py-0.5 rounded text-white">.xls</code> atau <code class="bg-white/10 px-1.5 py-0.5 rounded text-white">.xlsx</code>.</li>
            </ol>
        </div>

        <div class="mb-8">
            <a href="/admin/siswa/template" class="inline-flex items-center gap-2 bg-white/5 border border-border text-text font-bold py-3 px-6 rounded-xl hover:bg-white/10 transition-all">
                📄 Download Template Excel
            </a>
        </div>

        <div class="h-px bg-border my-8"></div>

        <form method="POST" action="/admin/siswa/import" enctype="multipart/form-data" class="flex flex-col gap-6 max-w-lg">
            <?= csrf_field() ?>
            
            <div class="flex flex-col gap-1.5">
                <label class="text-[0.75rem] font-bold text-text-muted uppercase tracking-wider pl-1" for="excel_file">Upload File Excel <span class="text-red-500">*</span></label>
                <input type="file" id="excel_file" name="excel_file" class="w-full bg-white/5 border border-border rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-primary transition-all file:mr-4 file:py-1 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-black file:bg-primary/20 file:text-primary hover:file:bg-primary/30 file:cursor-pointer" accept=".xls,.xlsx" required>
            </div>

            <div class="mt-2">
                <button type="submit" class="bg-primary text-white font-bold py-3 px-8 rounded-xl hover:opacity-90 hover:-translate-y-0.5 transition-all cursor-pointer">
                    🚀 Proses Import Data
                </button>
            </div>
        </form>
    </div>
</div>
