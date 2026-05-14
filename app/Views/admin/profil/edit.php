<?php
$errors = flash('errors') ?? [];
$old    = fn(string $k) => old($k, $profil[$k] ?? '');
$err    = fn(string $k) => $errors[$k][0] ?? null;
$cls    = fn(string $k) => $err($k) ? 'form-input is-invalid' : 'form-input';
?>

<div class="card">
    <div class="card-header">
        <h2 class="card-title">🏫 Pengaturan Profil Sekolah</h2>
    </div>
    <div class="card-body">
        <form method="POST" action="/admin/profil" enctype="multipart/form-data" novalidate>
            <?= csrf_field() ?>

            <div class="form-grid">

                <!-- Nama Sekolah -->
                <div class="form-group form-group-wide">
                    <label class="form-label" for="nama_sekolah">Nama Sekolah <span class="required">*</span></label>
                    <input type="text" id="nama_sekolah" name="nama_sekolah"
                           class="<?= $cls('nama_sekolah') ?>"
                           value="<?= e($old('nama_sekolah')) ?>"
                           required>
                    <?php if ($e = $err('nama_sekolah')): ?>
                        <span class="form-error"><?= e($e) ?></span>
                    <?php endif; ?>
                </div>

                <!-- Logo -->
                <div class="form-group form-group-wide">
                    <label class="form-label" for="logo">Logo Sekolah</label>
                    <?php if (!empty($profil['logo'])): ?>
                        <div style="margin-bottom: 1rem;">
                            <img src="<?= url($profil['logo']) ?>" alt="Logo Sekolah" style="max-height: 100px; border-radius: 8px;">
                        </div>
                    <?php endif; ?>
                    <input type="file" id="logo" name="logo"
                           class="<?= $cls('logo') ?>"
                           accept="image/*">
                    <small class="text-muted">Biarkan kosong jika tidak ingin mengubah logo. Format didukung: JPG, PNG, SVG, WEBP.</small>
                    <?php if ($e = $err('logo')): ?>
                        <span class="form-error"><?= e($e) ?></span>
                    <?php endif; ?>
                </div>

                <!-- Kepala Sekolah -->
                <div class="form-group">
                    <label class="form-label" for="kepala_sekolah">Kepala Sekolah</label>
                    <input type="text" id="kepala_sekolah" name="kepala_sekolah"
                           class="<?= $cls('kepala_sekolah') ?>"
                           value="<?= e($old('kepala_sekolah')) ?>">
                    <?php if ($e = $err('kepala_sekolah')): ?>
                        <span class="form-error"><?= e($e) ?></span>
                    <?php endif; ?>
                </div>

                <!-- NIP Kepala Sekolah -->
                <div class="form-group">
                    <label class="form-label" for="nip_kepala_sekolah">NIP Kepala Sekolah</label>
                    <input type="text" id="nip_kepala_sekolah" name="nip_kepala_sekolah"
                           class="<?= $cls('nip_kepala_sekolah') ?>"
                           value="<?= e($old('nip_kepala_sekolah')) ?>">
                    <?php if ($e = $err('nip_kepala_sekolah')): ?>
                        <span class="form-error"><?= e($e) ?></span>
                    <?php endif; ?>
                </div>

                <!-- Email -->
                <div class="form-group">
                    <label class="form-label" for="email">Email</label>
                    <input type="email" id="email" name="email"
                           class="<?= $cls('email') ?>"
                           value="<?= e($old('email')) ?>">
                    <?php if ($e = $err('email')): ?>
                        <span class="form-error"><?= e($e) ?></span>
                    <?php endif; ?>
                </div>

                <!-- Telepon -->
                <div class="form-group">
                    <label class="form-label" for="telepon">Telepon / WhatsApp</label>
                    <input type="text" id="telepon" name="telepon"
                           class="<?= $cls('telepon') ?>"
                           value="<?= e($old('telepon')) ?>">
                    <?php if ($e = $err('telepon')): ?>
                        <span class="form-error"><?= e($e) ?></span>
                    <?php endif; ?>
                </div>

                <!-- Website -->
                <div class="form-group">
                    <label class="form-label" for="website">Website</label>
                    <input type="url" id="website" name="website"
                           class="<?= $cls('website') ?>"
                           value="<?= e($old('website')) ?>">
                    <?php if ($e = $err('website')): ?>
                        <span class="form-error"><?= e($e) ?></span>
                    <?php endif; ?>
                </div>

                <!-- Alamat -->
                <div class="form-group form-group-wide">
                    <label class="form-label" for="alamat">Alamat Lengkap</label>
                    <textarea id="alamat" name="alamat" rows="3"
                              class="<?= $cls('alamat') ?>"><?= e($old('alamat')) ?></textarea>
                    <?php if ($e = $err('alamat')): ?>
                        <span class="form-error"><?= e($e) ?></span>
                    <?php endif; ?>
                </div>

                <!-- Waktu Pengumuman -->
                <div class="form-group form-group-wide">
                    <label class="form-label" for="tgl_pengumuman">Waktu Pengumuman Kelulusan</label>
                    <?php
                    $rawTgl = $old('tgl_pengumuman');
                    $tglVal = $rawTgl ? date('Y-m-d\TH:i', strtotime($rawTgl)) : '';
                    ?>
                    <input type="datetime-local" id="tgl_pengumuman" name="tgl_pengumuman"
                           class="<?= $cls('tgl_pengumuman') ?>"
                           value="<?= e($tglVal) ?>">
                    <small class="text-muted">Kosongkan jika pengumuman sudah bisa diakses kapan saja. Jika diisi, siswa akan melihat hitung mundur (countdown) sampai waktu yang ditentukan.</small>
                    <?php if ($e = $err('tgl_pengumuman')): ?>
                        <span class="form-error"><?= e($e) ?></span>
                    <?php endif; ?>
                </div>

                <!-- Warna Dasar -->
                <div class="form-group">
                    <label class="form-label" for="warna_dasar">Warna Dasar Sistem (UI)</label>
                    <div style="display: flex; align-items: center; gap: 1rem;">
                        <input type="color" id="warna_dasar" name="warna_dasar"
                               class="<?= $cls('warna_dasar') ?>" style="padding: 0; width: 50px; height: 50px; border: none; cursor: pointer;"
                               value="<?= e($old('warna_dasar') ?: '#6366f1') ?>">
                        <small class="text-muted">Pilih warna dasar aplikasi agar sesuai dengan warna ciri khas sekolah.</small>
                    </div>
                    <?php if ($e = $err('warna_dasar')): ?>
                        <span class="form-error"><?= e($e) ?></span>
                    <?php endif; ?>
                </div>

                <hr style="border: 0; border-top: 1px solid var(--clr-border); margin: 2rem 0;">
                <h3 style="grid-column: 1 / -1; margin-bottom: 0;">Pengaturan Template SKL</h3>

                <!-- Template Header -->
                <div class="form-group form-group-wide" style="margin-top: 1rem;">
                    <label class="form-label" for="template_header">Kop Surat (Header)</label>
                    <small class="text-muted" style="display:block; margin-bottom:.5rem;">Gunakan tag: <code>[nama_sekolah]</code>, <code>[alamat]</code>, <code>[website]</code>, <code>[email]</code>, <code>[telepon]</code>, <code>[logo_sekolah]</code></small>
                    <textarea id="template_header" name="template_header" rows="8"
                              class="tinymce"><?= e($old('template_header')) ?></textarea>
                </div>

                <!-- Template Surat -->
                <div class="form-group form-group-wide" style="margin-top: 1rem;">
                    <label class="form-label" for="template_surat">Isi Surat (Body)</label>
                    <small class="text-muted" style="display:block; margin-bottom:.5rem;">Gunakan tag: <code>[nama_siswa]</code>, <code>[nisn]</code>, <code>[tempat_lahir]</code>, <code>[tanggal_lahir]</code>, <code>[jenis_kelamin]</code>, <code>[jurusan]</code>, <code>[nilai_rata_rata]</code>, <code>[status_kelulusan]</code>, <code>[tahun_pelajaran]</code>, <code>[nama_sekolah]</code></small>
                    <textarea id="template_surat" name="template_surat" rows="15"
                              class="tinymce"><?= e($old('template_surat')) ?></textarea>
                </div>

                <!-- Template Footer -->
                <div class="form-group form-group-wide" style="margin-top: 1rem;">
                    <label class="form-label" for="template_footer">Tanda Tangan (Footer)</label>
                    <small class="text-muted" style="display:block; margin-bottom:.5rem;">Gunakan tag: <code>[tanggal_surat]</code>, <code>[kepala_sekolah]</code>, <code>[nip_kepala_sekolah]</code></small>
                    <textarea id="template_footer" name="template_footer" rows="8"
                              class="tinymce"><?= e($old('template_footer')) ?></textarea>
                </div>

            </div>

            <div class="form-actions" style="margin-top: 1.5rem; justify-content: flex-start;">
                <button type="submit" class="btn btn-primary">
                    💾 Simpan Profil Sekolah
                </button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.3/tinymce.min.js" referrerpolicy="origin"></script>
<script>
    tinymce.init({
        selector: '.tinymce',
        plugins: 'table lists code image',
        toolbar: 'undo redo | blocks | bold italic | alignleft aligncenter alignright alignjustify | table | bullist numlist | code image',
        menubar: false,
        branding: false,
        promotion: false,
        content_style: "body { font-family: 'Times New Roman', Times, serif; font-size: 12pt; }"
    });
</script>
