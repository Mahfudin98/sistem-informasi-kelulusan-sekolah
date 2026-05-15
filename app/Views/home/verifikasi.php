<div class="max-w-2xl mx-auto my-12 animate-fade-in">
    <div class="bg-white border border-slate-200 rounded-3xl shadow-xl overflow-hidden">
        <div class="bg-emerald-600 p-8 text-white text-center">
            <div class="w-20 h-20 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-4 border-4 border-white/30">
                <span class="text-4xl">✅</span>
            </div>
            <h1 class="text-2xl font-black uppercase tracking-tight">Verifikasi Berhasil</h1>
            <p class="text-emerald-100 mt-2 font-medium">Dokumen ini dinyatakan asli dan terdaftar di sistem kami.</p>
        </div>

        <div class="p-8">
            <div class="flex items-center gap-4 mb-8 pb-6 border-b border-slate-100">
                <?php if ($profil['logo']): ?>
                    <img src="/<?= ltrim($profil['logo'], '/') ?>" alt="Logo" class="w-16 h-16 object-contain">
                <?php endif; ?>
                <div>
                    <h2 class="text-lg font-bold text-slate-800 uppercase leading-tight"><?= e($profil['nama_sekolah']) ?></h2>
                    <p class="text-xs text-slate-500 mt-1 font-medium"><?= e($profil['alamat']) ?></p>
                </div>
            </div>

            <h3 class="text-[0.7rem] font-black uppercase tracking-widest text-slate-400 mb-6 flex items-center gap-2">
                <span class="w-8 h-px bg-slate-200"></span> Informasi Siswa <span class="flex-1 h-px bg-slate-200"></span>
            </h3>

            <div class="grid gap-4">
                <div class="flex justify-between items-center py-2 border-b border-slate-50">
                    <span class="text-sm text-slate-500">Nama Lengkap</span>
                    <span class="text-sm font-bold text-slate-800"><?= e($siswa['nama']) ?></span>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-slate-50">
                    <span class="text-sm text-slate-500">NISN</span>
                    <span class="text-sm font-mono font-bold text-slate-800"><?= e($siswa['nisn']) ?></span>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-slate-50">
                    <span class="text-sm text-slate-500">Tahun Lulus</span>
                    <span class="text-sm font-bold text-emerald-600"><?= e($siswa['tahun_lulus']) ?></span>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-slate-50">
                    <span class="text-sm text-slate-500">Status Kelulusan</span>
                    <?php if ($siswa['status_kelulusan'] === 'lulus'): ?>
                        <span class="px-3 py-1 bg-emerald-100 text-emerald-700 rounded-full text-[0.65rem] font-black uppercase tracking-wider">Lulus</span>
                    <?php else: ?>
                        <span class="px-3 py-1 bg-rose-100 text-rose-700 rounded-full text-[0.65rem] font-black uppercase tracking-wider">Tidak Lulus</span>
                    <?php endif; ?>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-slate-50">
                    <span class="text-sm text-slate-500">Nilai Rata-rata</span>
                    <span class="text-sm font-bold text-slate-800"><?= number_format((float)$siswa['nilai_rata_rata'], 2) ?></span>
                </div>
            </div>

            <div class="mt-10 p-4 bg-slate-50 rounded-2xl text-center">
                <p class="text-[0.65rem] text-slate-400 uppercase font-black tracking-widest mb-1">Keamanan Sistem</p>
                <p class="text-[0.7rem] text-slate-500 font-medium italic">Data ini ditarik secara langsung dari server database sekolah pada <?= date('d/m/Y H:i') ?>.</p>
            </div>
        </div>
        
        <div class="p-6 bg-slate-50 border-t border-slate-100 text-center">
            <a href="/" class="text-sm font-bold text-emerald-600 hover:text-emerald-700 flex items-center justify-center gap-2">
                <span>🏠</span> Kembali ke Beranda
            </a>
        </div>
    </div>
</div>
