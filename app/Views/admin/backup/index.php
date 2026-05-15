<div class="max-w-4xl mx-auto animate-fade-in">
    <div class="mb-8">
        <h1 class="text-3xl font-black text-text tracking-tight">💾 Backup Database</h1>
        <p class="text-text-muted mt-2">Ekspor data Anda ke format SQL untuk cadangan atau pemindahan server.</p>
    </div>

    <form action="/admin/backup/download" method="POST" class="grid gap-8 lg:grid-cols-3">
        <?= csrf_field() ?>

        <!-- Configuration -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-surface border border-border rounded-2xl p-6 shadow-sm">
                <h2 class="text-lg font-bold mb-4 flex items-center gap-2">
                    <span class="w-8 h-8 bg-primary/10 text-primary rounded-lg flex items-center justify-center text-sm">📋</span>
                    Pilih Tabel
                </h2>
                <p class="text-xs text-text-muted mb-4 uppercase font-black tracking-widest">Kosongkan untuk mengekspor semua tabel</p>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <?php foreach ($tables as $table): ?>
                        <label class="flex items-center gap-3 p-3 rounded-xl border border-border hover:bg-slate-50 transition-all cursor-pointer group">
                            <input type="checkbox" name="tables[]" value="<?= e($table) ?>" class="w-5 h-5 rounded border-border text-primary focus:ring-primary">
                            <div class="flex flex-col">
                                <span class="font-bold text-sm text-text group-hover:text-primary transition-colors"><?= e($table) ?></span>
                                <span class="text-[0.65rem] text-text-muted uppercase font-medium">Table Structure & Data</span>
                            </div>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="bg-surface border border-border rounded-2xl p-6 shadow-sm">
                <h2 class="text-lg font-bold mb-4 flex items-center gap-2">
                    <span class="w-8 h-8 bg-amber-500/10 text-amber-600 rounded-lg flex items-center justify-center text-sm">⏳</span>
                    Filter Data Siswa
                </h2>
                <p class="text-sm text-text-muted mb-4">Anda dapat mengekspor hanya siswa dari tahun kelulusan tertentu.</p>
                
                <div class="max-w-xs">
                    <label class="text-[0.7rem] font-black text-text-muted uppercase tracking-widest block mb-2" for="tahun_lulus">Tahun Lulus</label>
                    <select name="tahun_lulus" id="tahun_lulus" class="w-full bg-white border border-border rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all">
                        <option value="">-- Semua Tahun --</option>
                        <?php foreach ($years as $year): ?>
                            <option value="<?= $year ?>"><?= $year ?></option>
                        <?php endforeach; ?>
                    </select>
                    <p class="text-[0.65rem] text-amber-600 mt-2 italic">* Filter ini hanya berlaku jika tabel <b>siswa</b> terpilih atau tidak ada tabel yang dipilih.</p>
                </div>
            </div>
        </div>

        <!-- Sidebar Actions -->
        <div class="space-y-6">
            <div class="bg-primary text-white rounded-3xl p-8 shadow-xl shadow-primary/20 relative overflow-hidden group">
                <div class="relative z-10">
                    <h3 class="text-xl font-black mb-2">Mulai Backup</h3>
                    <p class="text-primary-text/80 text-sm mb-6 leading-relaxed">Pastikan Anda menyimpan file hasil ekspor di tempat yang aman.</p>
                    
                    <button type="submit" class="w-full bg-white text-primary font-black py-4 rounded-2xl shadow-lg hover:scale-[1.02] active:scale-[0.98] transition-all flex items-center justify-center gap-3">
                        <span>🚀</span> Unduh SQL
                    </button>
                </div>
                <!-- Decorative background -->
                <div class="absolute -right-4 -bottom-4 text-white/10 text-9xl font-black group-hover:rotate-12 transition-transform duration-700">DB</div>
            </div>

            <div class="bg-surface border border-border rounded-2xl p-6">
                <h4 class="text-[0.7rem] font-black text-text-muted uppercase tracking-widest mb-4">Informasi File</h4>
                <div class="space-y-3">
                    <div class="flex justify-between text-sm">
                        <span class="text-text-muted">Format</span>
                        <span class="font-bold text-text">.sql (SQL Dump)</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-text-muted">Metode</span>
                        <span class="font-bold text-text">INSERT Statements</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-text-muted">Charset</span>
                        <span class="font-bold text-text">UTF-8</span>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
