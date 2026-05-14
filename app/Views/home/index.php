<!-- ── Hero ──────────────────────────────────────────────────────────────── -->
<section class="relative overflow-hidden py-16 md:py-24 min-h-[80vh] flex items-center">
    <div class="absolute inset-0 z-0 bg-[radial-gradient(ellipse_60%_50%_at_50%_-10%,rgba(99,102,241,0.05),transparent)]"></div>
    <div class="container relative z-10 text-center mx-auto px-6">
        <div class="inline-flex items-center gap-2 bg-primary/10 border border-primary/20 text-primary px-4 py-1.5 rounded-full text-sm font-bold mb-6">
            🎓 Sistem Kelulusan Online — <?= e(profil_sekolah('nama_sekolah', env('APP_NAME'))) ?>
        </div>
        <h1 class="text-4xl md:text-6xl font-black leading-tight mb-4 tracking-tight">
            Cek Status <span class="text-primary">Kelulusan</span> Kamu
        </h1>
        <p class="text-text-muted text-lg max-w-xl mx-auto mb-10">
            Masukkan NISN (Nomor Induk Siswa Nasional) untuk mengetahui
            status kelulusan kamu secara cepat dan akurat.
        </p>

        <!-- Lookup Form or Timer -->
        <?php
        $now = time();
        $isWaiting = !empty($profil['tgl_pengumuman']) && strtotime($profil['tgl_pengumuman']) > $now;
        if ($isWaiting):
            $target = date('Y-m-d\TH:i:s', strtotime($profil['tgl_pengumuman']));
        ?>
        <div class="bg-surface border border-border rounded-3xl p-8 max-w-2xl mx-auto mb-8 shadow-lg text-center">
            <h2 class="text-2xl font-bold mb-4">Pengumuman Kelulusan Dibuka Dalam:</h2>
            <div id="countdown" class="flex gap-4 justify-center text-3xl md:text-5xl font-black text-primary">
                <!-- Timer inserted here via JS -->
            </div>
            <p class="mt-6 text-text-muted font-medium italic">Tanggal: <?= format_date($profil['tgl_pengumuman'], 'd F Y H:i') ?> WIB</p>
        </div>
        <script>
            const countDownDate = new Date("<?= $target ?>").getTime();
            const x = setInterval(function() {
                const now = new Date().getTime();
                const distance = countDownDate - now;
                if (distance < 0) {
                    clearInterval(x);
                    window.location.reload();
                    return;
                }
                const days = Math.floor(distance / (1000 * 60 * 60 * 24));
                const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((distance % (1000 * 60)) / 1000);
                
                document.getElementById("countdown").innerHTML = 
                    "<div>" + days + "<div class='text-xs font-bold text-text-muted uppercase tracking-widest mt-1'>Hari</div></div>" +
                    "<div>" + hours + "<div class='text-xs font-bold text-text-muted uppercase tracking-widest mt-1'>Jam</div></div>" +
                    "<div>" + minutes + "<div class='text-xs font-bold text-text-muted uppercase tracking-widest mt-1'>Menit</div></div>" +
                    "<div>" + seconds + "<div class='text-xs font-bold text-text-muted uppercase tracking-widest mt-1'>Detik</div></div>";
            }, 1000);
        </script>
        <?php else: ?>
        <div class="bg-surface border border-border rounded-3xl p-8 max-w-2xl mx-auto mb-8 shadow-xl">
            <form method="POST" action="/cek" class="flex flex-col gap-4" id="lookupForm" novalidate>
                <?= csrf_field() ?>
                <div class="flex flex-col lg:flex-row items-stretch lg:items-center gap-3 bg-bg border border-border rounded-2xl p-2 sm:pl-4 focus-within:border-primary focus-within:ring-4 focus-within:ring-primary/10 transition-all">
                    <div class="flex items-center gap-3 px-4 py-2 sm:p-0 flex-1">
                        <span class="text-xl shrink-0">🔍</span>
                        <input
                            type="text"
                            id="nisn"
                            name="nisn"
                            class="flex-1 bg-transparent border-none outline-none text-text text-lg font-bold placeholder:text-text-muted placeholder:font-normal min-w-0 w-full"
                            placeholder="Masukkan NISN"
                            value="<?= e($nisn ?? '') ?>"
                            maxlength="10"
                            inputmode="numeric"
                            pattern="\d{10}"
                            required
                            autocomplete="off"
                        >
                    </div>
                    <button type="submit" class="bg-primary text-white font-bold py-2 px-8 rounded-xl hover:opacity-90 transition-all shrink-0 cursor-pointer disabled:opacity-50 shadow-md shadow-primary/20" id="submitBtn">
                        <span class="btn-text">Cek Sekarang</span>
                        <span class="btn-spinner hidden" aria-hidden="true">⟳</span>
                    </button>
                </div>
                <p class="text-text-muted text-sm text-left pl-4 font-medium italic">Contoh: 1234567890</p>
            </form>
        </div>
        <?php endif; ?>

        <!-- ── Result Card ────────────────────────────────────────────────── -->
        <?php if (isset($result)): ?>
            <?php if ($result['found']): ?>
                <?php $s = $result['siswa']; ?>
                <?php $lulus = $s['status_kelulusan'] === 'lulus'; ?>

                <div class="max-w-2xl mx-auto rounded-3xl p-10 border bg-surface animate-slide-up shadow-2xl <?= $lulus ? 'border-success' : 'border-danger' ?>" id="resultCard">
                    <div class="text-7xl mb-6 leading-none">
                        <?= $lulus ? '🎉' : '😔' ?>
                    </div>
                    <div class="text-4xl font-black tracking-widest mb-2 <?= $lulus ? 'text-success' : 'text-danger' ?>">
                        <?= $lulus ? 'LULUS' : 'TIDAK LULUS' ?>
                    </div>
                    <p class="text-text-muted mb-8 font-medium"><?= $result['message'] ?></p>

                    <div class="bg-bg border border-border rounded-2xl p-8">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div class="flex flex-col gap-1 text-left">
                                <span class="text-[0.7rem] font-bold text-text-muted uppercase tracking-widest">Nama Lengkap</span>
                                <span class="font-extrabold text-lg"><?= e($s['nama']) ?></span>
                            </div>
                            <div class="flex flex-col gap-1 text-left">
                                <span class="text-[0.7rem] font-bold text-text-muted uppercase tracking-widest">NISN</span>
                                <span class="font-extrabold text-lg"><?= e($s['nisn']) ?></span>
                            </div>
                            <?php if (!empty($s['jurusan']) && trim($s['jurusan']) !== '-'): ?>
                            <div class="flex flex-col gap-1 text-left">
                                <span class="text-[0.7rem] font-bold text-text-muted uppercase tracking-widest">Jurusan</span>
                                <span class="font-extrabold text-lg"><?= e($s['jurusan']) ?></span>
                            </div>
                            <?php endif; ?>
                            <div class="flex flex-col gap-1 text-left">
                                <span class="text-[0.7rem] font-bold text-text-muted uppercase tracking-widest">Tahun Lulus</span>
                                <span class="font-extrabold text-lg"><?= e($s['tahun_lulus']) ?></span>
                            </div>
                            <?php if ($s['nilai_rata_rata']): ?>
                            <div class="flex flex-col gap-1 text-left">
                                <span class="text-[0.7rem] font-bold text-text-muted uppercase tracking-widest">Nilai Rata-rata</span>
                                <span class="font-extrabold text-lg text-primary"><?= number_format((float)$s['nilai_rata_rata'], 2) ?></span>
                            </div>
                            <?php endif; ?>
                            <?php if ($s['keterangan']): ?>
                            <div class="flex flex-col gap-1 text-left sm:col-span-2 border-t border-border pt-4 mt-2">
                                <span class="text-[0.7rem] font-bold text-text-muted uppercase tracking-widest">Keterangan Tambahan</span>
                                <span class="font-medium text-sm text-text"><?= e($s['keterangan']) ?></span>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="mt-10 text-center">
                        <a href="/cetak/<?= e($s['nisn']) ?>" target="_blank" class="bg-primary text-white font-bold py-4 px-10 rounded-xl hover:opacity-90 hover:-translate-y-0.5 transition-all inline-flex items-center gap-3 shadow-lg shadow-primary/20">
                            🖨️ Cetak Surat Keterangan (SKL)
                        </a>
                    </div>
                </div>

            <?php else: ?>

                <div class="max-w-2xl mx-auto rounded-3xl p-10 border bg-surface border-primary/20 shadow-xl animate-slide-up" id="resultCard">
                    <div class="text-6xl mb-6 leading-none">❓</div>
                    <p class="text-text-muted mb-4 font-bold"><?= $result['message'] ?></p>
                    <p class="text-text-muted text-sm italic">Pastikan NISN yang kamu masukkan sudah benar. Hubungi pihak sekolah jika data tidak ditemukan.</p>
                </div>

            <?php endif; ?>
        <?php endif; ?>
    </div>
</section>

<!-- ── Info Section ──────────────────────────────────────────────────────── -->
<section class="py-20 bg-bg">
    <div class="container mx-auto px-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="bg-surface border border-border rounded-3xl p-10 text-center hover:shadow-xl hover:-translate-y-2 transition-all">
                <div class="w-16 h-16 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center text-3xl mx-auto mb-6 shadow-sm">🔒</div>
                <h3 class="text-xl font-bold mb-3">Aman & Terpercaya</h3>
                <p class="text-text-muted text-sm leading-relaxed">Data kelulusan bersumber langsung dari sistem administrasi resmi sekolah yang telah divalidasi.</p>
            </div>
            <div class="bg-surface border border-border rounded-3xl p-10 text-center hover:shadow-xl hover:-translate-y-2 transition-all">
                <div class="w-16 h-16 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center text-3xl mx-auto mb-6 shadow-sm">⚡</div>
                <h3 class="text-xl font-bold mb-3">Cepat & Akurat</h3>
                <p class="text-text-muted text-sm leading-relaxed">Hasil pencarian ditampilkan secara instan tanpa perlu memproses data yang rumit atau menunggu lama.</p>
            </div>
            <div class="bg-surface border border-border rounded-3xl p-10 text-center hover:shadow-xl hover:-translate-y-2 transition-all">
                <div class="w-16 h-16 bg-purple-50 text-purple-600 rounded-2xl flex items-center justify-center text-3xl mx-auto mb-6 shadow-sm">📱</div>
                <h3 class="text-xl font-bold mb-3">Akses Fleksibel</h3>
                <p class="text-text-muted text-sm leading-relaxed">Platform didesain responsif untuk memudahkan akses dari smartphone, tablet, maupun perangkat komputer.</p>
            </div>
        </div>
    </div>
</section>
