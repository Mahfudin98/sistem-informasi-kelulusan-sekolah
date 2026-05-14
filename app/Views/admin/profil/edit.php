<?php
$errors = flash('errors') ?? [];
$old    = fn(string $k) => old($k, $profil[$k] ?? '');
$err    = fn(string $k) => $errors[$k][0] ?? null;
$input_base = "w-full bg-white/5 border border-border rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-primary focus:ring-4 focus:ring-primary/20 transition-all";
$cls    = fn(string $k) => $err($k) ? $input_base . ' border-red-500/50 ring-4 ring-red-500/10' : $input_base;
?>

<div class="bg-surface border border-border rounded-2xl overflow-hidden shadow-sm animate-fade-in">
    <div class="flex items-center justify-between p-6 border-b border-border">
        <h2 class="text-lg font-bold">🏫 Pengaturan Profil Sekolah</h2>
    </div>
    <div class="p-6">
        <form method="POST" action="/admin/profil" enctype="multipart/form-data" class="flex flex-col gap-8" novalidate>
            <?= csrf_field() ?>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <!-- Nama Sekolah -->
                <div class="flex flex-col gap-1.5 md:col-span-2">
                    <label class="text-[0.75rem] font-bold text-text-muted uppercase tracking-wider pl-1" for="nama_sekolah">Nama Sekolah <span class="text-red-500">*</span></label>
                    <input type="text" id="nama_sekolah" name="nama_sekolah"
                           class="<?= $cls('nama_sekolah') ?>"
                           value="<?= e($old('nama_sekolah')) ?>"
                           required>
                    <?php if ($e = $err('nama_sekolah')): ?>
                        <span class="text-[0.7rem] text-red-300 pl-1"><?= e($e) ?></span>
                    <?php endif; ?>
                </div>

                <!-- Logo -->
                <div class="flex flex-col gap-1.5 md:col-span-2">
                    <label class="text-[0.75rem] font-bold text-text-muted uppercase tracking-wider pl-1" for="logo">Logo Sekolah</label>
                    <?php if (!empty($profil['logo'])): ?>
                        <div class="mb-4">
                            <img src="<?= url($profil['logo']) ?>" alt="Logo Sekolah" class="max-h-24 rounded-lg bg-white/5 p-2 border border-border">
                        </div>
                    <?php endif; ?>
                    <input type="file" id="logo" name="logo"
                           class="<?= $cls('logo') ?> file:mr-4 file:py-1 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-black file:bg-primary/20 file:text-primary hover:file:bg-primary/30 file:cursor-pointer"
                           accept="image/*">
                    <p class="text-[0.7rem] text-text-muted mt-1 italic pl-1">Biarkan kosong jika tidak ingin mengubah logo. Format didukung: JPG, PNG, SVG, WEBP.</p>
                    <?php if ($e = $err('logo')): ?>
                        <span class="text-[0.7rem] text-red-300 pl-1"><?= e($e) ?></span>
                    <?php endif; ?>
                </div>

                <!-- Kepala Sekolah -->
                <div class="flex flex-col gap-1.5">
                    <label class="text-[0.75rem] font-bold text-text-muted uppercase tracking-wider pl-1" for="kepala_sekolah">Kepala Sekolah</label>
                    <input type="text" id="kepala_sekolah" name="kepala_sekolah"
                           class="<?= $cls('kepala_sekolah') ?>"
                           value="<?= e($old('kepala_sekolah')) ?>">
                    <?php if ($e = $err('kepala_sekolah')): ?>
                        <span class="text-[0.7rem] text-red-300 pl-1"><?= e($e) ?></span>
                    <?php endif; ?>
                </div>

                <!-- NIP Kepala Sekolah -->
                <div class="flex flex-col gap-1.5">
                    <label class="text-[0.75rem] font-bold text-text-muted uppercase tracking-wider pl-1" for="nip_kepala_sekolah">NIP Kepala Sekolah</label>
                    <input type="text" id="nip_kepala_sekolah" name="nip_kepala_sekolah"
                           class="<?= $cls('nip_kepala_sekolah') ?>"
                           value="<?= e($old('nip_kepala_sekolah')) ?>">
                    <?php if ($e = $err('nip_kepala_sekolah')): ?>
                        <span class="text-[0.7rem] text-red-300 pl-1"><?= e($e) ?></span>
                    <?php endif; ?>
                </div>

                <!-- Email -->
                <div class="flex flex-col gap-1.5">
                    <label class="text-[0.75rem] font-bold text-text-muted uppercase tracking-wider pl-1" for="email">Email</label>
                    <input type="email" id="email" name="email"
                           class="<?= $cls('email') ?>"
                           value="<?= e($old('email')) ?>">
                    <?php if ($e = $err('email')): ?>
                        <span class="text-[0.7rem] text-red-300 pl-1"><?= e($e) ?></span>
                    <?php endif; ?>
                </div>

                <!-- Telepon -->
                <div class="flex flex-col gap-1.5">
                    <label class="text-[0.75rem] font-bold text-text-muted uppercase tracking-wider pl-1" for="telepon">Telepon / WhatsApp</label>
                    <input type="text" id="telepon" name="telepon"
                           class="<?= $cls('telepon') ?>"
                           value="<?= e($old('telepon')) ?>">
                    <?php if ($e = $err('telepon')): ?>
                        <span class="text-[0.7rem] text-red-300 pl-1"><?= e($e) ?></span>
                    <?php endif; ?>
                </div>

                <!-- Website -->
                <div class="flex flex-col gap-1.5">
                    <label class="text-[0.75rem] font-bold text-text-muted uppercase tracking-wider pl-1" for="website">Website</label>
                    <input type="url" id="website" name="website"
                           class="<?= $cls('website') ?>"
                           value="<?= e($old('website')) ?>">
                    <?php if ($e = $err('website')): ?>
                        <span class="text-[0.7rem] text-red-300 pl-1"><?= e($e) ?></span>
                    <?php endif; ?>
                </div>

                <!-- Alamat -->
                <div class="flex flex-col gap-1.5 md:col-span-2">
                    <label class="text-[0.75rem] font-bold text-text-muted uppercase tracking-wider pl-1" for="alamat">Alamat Lengkap</label>
                    <textarea id="alamat" name="alamat" rows="3"
                               class="<?= $cls('alamat') ?>"><?= e($old('alamat')) ?></textarea>
                    <?php if ($e = $err('alamat')): ?>
                        <span class="text-[0.7rem] text-red-300 pl-1"><?= e($e) ?></span>
                    <?php endif; ?>
                </div>

                <!-- Waktu Pengumuman -->
                <div class="flex flex-col gap-1.5 md:col-span-2">
                    <label class="text-[0.75rem] font-bold text-text-muted uppercase tracking-wider pl-1" for="tgl_pengumuman">Waktu Pengumuman Kelulusan</label>
                    <?php
                    $rawTgl = $old('tgl_pengumuman');
                    $tglVal = $rawTgl ? date('Y-m-d\TH:i', strtotime($rawTgl)) : '';
                    ?>
                    <input type="datetime-local" id="tgl_pengumuman" name="tgl_pengumuman"
                           class="<?= $cls('tgl_pengumuman') ?>"
                           value="<?= e($tglVal) ?>">
                    <p class="text-[0.7rem] text-text-muted mt-1 italic pl-1">Kosongkan jika pengumuman sudah bisa diakses kapan saja. Jika diisi, siswa akan melihat hitung mundur (countdown) sampai waktu yang ditentukan.</p>
                    <?php if ($e = $err('tgl_pengumuman')): ?>
                        <span class="text-[0.7rem] text-red-300 pl-1"><?= e($e) ?></span>
                    <?php endif; ?>
                </div>

                <!-- Warna Dasar -->
                <div class="flex flex-col gap-1.5">
                    <label class="text-[0.75rem] font-bold text-text-muted uppercase tracking-wider pl-1" for="warna_dasar">Warna Dasar Sistem (UI)</label>
                    <div class="flex items-center gap-4">
                        <input type="color" id="warna_dasar" name="warna_dasar"
                               class="w-12 h-12 rounded-lg bg-white/5 border border-border cursor-pointer"
                               value="<?= e($old('warna_dasar') ?: '#6366f1') ?>">
                        <p class="text-[0.7rem] text-text-muted italic">Pilih warna dasar aplikasi agar sesuai dengan warna ciri khas sekolah.</p>
                    </div>
                    <?php if ($e = $err('warna_dasar')): ?>
                        <span class="text-[0.7rem] text-red-300 pl-1"><?= e($e) ?></span>
                    <?php endif; ?>
                </div>

                <div class="md:col-span-2 h-px bg-border my-4"></div>
                <h3 class="md:col-span-2 text-lg font-bold">Pengaturan Template SKL</h3>

                <!-- Template Header -->
                <div class="flex flex-col gap-1.5 md:col-span-2 mt-2">
                    <label class="text-[0.75rem] font-bold text-text-muted uppercase tracking-wider pl-1" for="template_header">Kop Surat (Header)</label>
                    <p class="text-[0.7rem] text-indigo-300 mb-2 pl-1">Tag: <code>[nama_sekolah]</code>, <code>[alamat]</code>, <code>[website]</code>, <code>[email]</code>, <code>[telepon]</code>, <code>[logo_sekolah]</code></p>
                    <textarea id="template_header" name="template_header" rows="8"
                               class="tinymce"><?= e($old('template_header')) ?></textarea>
                </div>

                <!-- Template Surat -->
                <div class="flex flex-col gap-1.5 md:col-span-2 mt-4">
                    <label class="text-[0.75rem] font-bold text-text-muted uppercase tracking-wider pl-1" for="template_surat">Isi Surat (Body)</label>
                    <p class="text-[0.7rem] text-indigo-300 mb-2 pl-1">Tag: <code>[nama_siswa]</code>, <code>[nisn]</code>, <code>[tempat_lahir]</code>, <code>[tanggal_lahir]</code>, <code>[jenis_kelamin]</code>, <code>[jurusan]</code>, <code>[nilai_rata_rata]</code>, <code>[status_kelulusan]</code>, <code>[tahun_pelajaran]</code>, <code>[nama_sekolah]</code></p>
                    <textarea id="template_surat" name="template_surat" rows="15"
                               class="tinymce"><?= e($old('template_surat')) ?></textarea>
                </div>

                <!-- Template Footer -->
                <div class="flex flex-col gap-1.5 md:col-span-2 mt-4">
                    <label class="text-[0.75rem] font-bold text-text-muted uppercase tracking-wider pl-1" for="template_footer">Tanda Tangan (Footer)</label>
                    <p class="text-[0.7rem] text-indigo-300 mb-2 pl-1">Tag: <code>[tanggal_surat]</code>, <code>[kepala_sekolah]</code>, <code>[nip_kepala_sekolah]</code></p>
                    <textarea id="template_footer" name="template_footer" rows="8"
                               class="tinymce"><?= e($old('template_footer')) ?></textarea>
                </div>

            </div>

            <div class="mt-6">
                <button type="submit" class="bg-primary text-white font-bold py-3 px-8 rounded-xl hover:opacity-90 hover:-translate-y-0.5 transition-all cursor-pointer">
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
