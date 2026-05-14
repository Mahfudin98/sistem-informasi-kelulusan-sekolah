<!-- ── Hero ──────────────────────────────────────────────────────────────── -->
<section class="hero">
    <div class="hero-bg"></div>
    <div class="container hero-inner">
        <div class="hero-badge">🎓 Sistem Kelulusan Online — <?= e(profil_sekolah('nama_sekolah', env('APP_NAME'))) ?></div>
        <h1 class="hero-title">Cek Status <span class="gradient-text">Kelulusan</span> Kamu</h1>
        <p class="hero-subtitle">
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
        <div class="lookup-card" style="text-align: center;">
            <h2 style="margin-bottom: 1rem; font-size: 1.5rem; color: var(--clr-text);">Pengumuman Kelulusan Dibuka Dalam:</h2>
            <div id="countdown" style="display: flex; gap: 1rem; justify-content: center; font-size: 2rem; font-weight: 800; color: var(--clr-primary);">
                <!-- Timer inserted here via JS -->
            </div>
            <p style="margin-top: 1rem; color: var(--clr-text-muted);">Tanggal: <?= format_date($profil['tgl_pengumuman'], 'd F Y H:i') ?> WIB</p>
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
                    "<div>" + days + "<div style='font-size: 0.8rem; font-weight: normal; color: var(--clr-text-muted);'>Hari</div></div>" +
                    "<div>" + hours + "<div style='font-size: 0.8rem; font-weight: normal; color: var(--clr-text-muted);'>Jam</div></div>" +
                    "<div>" + minutes + "<div style='font-size: 0.8rem; font-weight: normal; color: var(--clr-text-muted);'>Menit</div></div>" +
                    "<div>" + seconds + "<div style='font-size: 0.8rem; font-weight: normal; color: var(--clr-text-muted);'>Detik</div></div>";
            }, 1000);
        </script>
        <?php else: ?>
        <div class="lookup-card">
            <form method="POST" action="/cek" class="lookup-form" id="lookupForm" novalidate>
                <?= csrf_field() ?>
                <div class="input-group">
                    <span class="input-prefix">🔍</span>
                    <input
                        type="text"
                        id="nisn"
                        name="nisn"
                        class="lookup-input"
                        placeholder="Masukkan NISN (10 digit)"
                        value="<?= e($nisn ?? '') ?>"
                        maxlength="10"
                        inputmode="numeric"
                        pattern="\d{10}"
                        required
                        autocomplete="off"
                    >
                    <button type="submit" class="lookup-btn" id="submitBtn">
                        <span class="btn-text">Cek Sekarang</span>
                        <span class="btn-spinner hidden" aria-hidden="true">⟳</span>
                    </button>
                </div>
                <p class="input-hint">Contoh: 1234567890</p>
            </form>
        </div>
        <?php endif; ?>

        <!-- ── Result Card ────────────────────────────────────────────────── -->
        <?php if (isset($result)): ?>
            <?php if ($result['found']): ?>
                <?php $s = $result['siswa']; ?>
                <?php $lulus = $s['status_kelulusan'] === 'lulus'; ?>

                <div class="result-card result-<?= $lulus ? 'lulus' : 'tidak-lulus' ?>" id="resultCard">
                    <div class="result-badge">
                        <?= $lulus ? '🎉' : '😔' ?>
                    </div>
                    <div class="result-status">
                        <?= $lulus ? 'LULUS' : 'TIDAK LULUS' ?>
                    </div>
                    <p class="result-message"><?= $result['message'] ?></p>

                    <div class="result-details">
                        <div class="detail-grid">
                            <div class="detail-item">
                                <span class="detail-label">Nama</span>
                                <span class="detail-value"><?= e($s['nama']) ?></span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">NISN</span>
                                <span class="detail-value"><?= e($s['nisn']) ?></span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Jurusan</span>
                                <span class="detail-value"><?= e($s['jurusan']) ?></span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Tahun Lulus</span>
                                <span class="detail-value"><?= e($s['tahun_lulus']) ?></span>
                            </div>
                            <?php if ($s['nilai_rata_rata']): ?>
                            <div class="detail-item">
                                <span class="detail-label">Nilai Rata-rata</span>
                                <span class="detail-value"><?= number_format((float)$s['nilai_rata_rata'], 2) ?></span>
                            </div>
                            <?php endif; ?>
                            <?php if ($s['keterangan']): ?>
                            <div class="detail-item detail-full">
                                <span class="detail-label">Keterangan</span>
                                <span class="detail-value"><?= e($s['keterangan']) ?></span>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <?php if ($lulus): ?>
                    <div style="margin-top: 1.5rem; text-align: center;">
                        <a href="/cetak/<?= e($s['nisn']) ?>" target="_blank" class="btn btn-primary" style="padding: 0.75rem 2rem; font-size: 1rem; box-shadow: 0 4px 15px rgba(99,102,241,0.3);">
                            🖨️ Cetak Surat Keterangan Lulus (SKL)
                        </a>
                    </div>
                    <?php endif; ?>
                </div>

            <?php else: ?>

                <div class="result-card result-not-found" id="resultCard">
                    <div class="result-badge">❓</div>
                    <p class="result-message"><?= $result['message'] ?></p>
                    <p class="result-hint">Pastikan NISN yang kamu masukkan sudah benar.</p>
                </div>

            <?php endif; ?>
        <?php endif; ?>
    </div>
</section>

<!-- ── Info Section ──────────────────────────────────────────────────────── -->
<section class="info-section">
    <div class="container">
        <div class="info-grid">
            <div class="info-card">
                <div class="info-icon">🔒</div>
                <h3>Aman & Terpercaya</h3>
                <p>Data kelulusan bersumber langsung dari sistem administrasi sekolah.</p>
            </div>
            <div class="info-card">
                <div class="info-icon">⚡</div>
                <h3>Cepat & Akurat</h3>
                <p>Hasil ditampilkan secara real-time tanpa perlu menunggu lama.</p>
            </div>
            <div class="info-card">
                <div class="info-icon">📱</div>
                <h3>Akses Kapan Saja</h3>
                <p>Dapat diakses dari perangkat apapun — smartphone, tablet, maupun desktop.</p>
            </div>
        </div>
    </div>
</section>
