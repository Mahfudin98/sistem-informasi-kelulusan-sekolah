<?php
/**
 * Reusable Siswa form partial.
 * Used by both create and edit views.
 *
 * Required variables:
 *   $action  string — form POST action URL
 *   $siswa   array|null — existing student data (null for create)
 *   $errors  array — validation errors from session
 */
$errors = flash('errors') ?? [];
$old    = fn(string $k) => old($k, $siswa[$k] ?? '');
$err    = fn(string $k) => $errors[$k][0] ?? null;
$input_base = "w-full bg-white border border-border rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all";
$cls    = fn(string $k) => $err($k) ? $input_base . ' border-rose-500 ring-rose-500/10' : $input_base;
?>

<div class="bg-surface border border-border rounded-2xl overflow-hidden shadow-sm animate-fade-in">
    <div class="flex items-center justify-between p-6 border-b border-border">
        <h2 class="text-lg font-bold">
            <?= isset($siswa) ? '✏️ Edit Data Siswa' : '➕ Tambah Siswa Baru' ?>
        </h2>
        <a href="/admin/siswa" class="text-text-muted hover:text-primary text-sm font-bold transition-colors flex items-center gap-2">
            <span class="text-lg">←</span> Kembali ke List
        </a>
    </div>
    <div class="p-6">
        <form method="POST" action="<?= e($action) ?>" id="siswaForm" class="flex flex-col gap-8" novalidate>
            <?= csrf_field() ?>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <!-- NISN -->
                <div class="flex flex-col gap-2">
                    <label class="text-[0.75rem] font-black text-text-muted uppercase tracking-wider pl-1" for="nisn">NISN <span class="text-rose-500">*</span></label>
                    <input type="text" id="nisn" name="nisn"
                           class="<?= $cls('nisn') ?>"
                           value="<?= e($old('nisn')) ?>"
                           maxlength="10" inputmode="numeric"
                           placeholder="10 digit NISN">
                    <?php if ($e = $err('nisn')): ?>
                        <span class="text-[0.7rem] text-rose-600 font-bold pl-1"><?= e($e) ?></span>
                    <?php endif; ?>
                </div>

                <!-- Nama -->
                <div class="flex flex-col gap-2 md:col-span-2">
                    <label class="text-[0.75rem] font-black text-text-muted uppercase tracking-wider pl-1" for="nama">Nama Lengkap <span class="text-rose-500">*</span></label>
                    <input type="text" id="nama" name="nama"
                           class="<?= $cls('nama') ?>"
                           value="<?= e($old('nama')) ?>"
                           placeholder="Nama lengkap siswa">
                    <?php if ($e = $err('nama')): ?>
                        <span class="text-[0.7rem] text-rose-600 font-bold pl-1"><?= e($e) ?></span>
                    <?php endif; ?>
                </div>

                <!-- Tempat Lahir -->
                <div class="flex flex-col gap-2">
                    <label class="text-[0.75rem] font-black text-text-muted uppercase tracking-wider pl-1" for="tempat_lahir">Tempat Lahir <span class="text-rose-500">*</span></label>
                    <input type="text" id="tempat_lahir" name="tempat_lahir"
                           class="<?= $cls('tempat_lahir') ?>"
                           value="<?= e($old('tempat_lahir')) ?>"
                           placeholder="Kota kelahiran">
                    <?php if ($e = $err('tempat_lahir')): ?>
                        <span class="text-[0.7rem] text-rose-600 font-bold pl-1"><?= e($e) ?></span>
                    <?php endif; ?>
                </div>

                <!-- Tanggal Lahir -->
                <div class="flex flex-col gap-2">
                    <label class="text-[0.75rem] font-black text-text-muted uppercase tracking-wider pl-1" for="tanggal_lahir">Tanggal Lahir <span class="text-rose-500">*</span></label>
                    <input type="date" id="tanggal_lahir" name="tanggal_lahir"
                           class="<?= $cls('tanggal_lahir') ?>"
                           value="<?= e($old('tanggal_lahir')) ?>">
                    <?php if ($e = $err('tanggal_lahir')): ?>
                        <span class="text-[0.7rem] text-rose-600 font-bold pl-1"><?= e($e) ?></span>
                    <?php endif; ?>
                </div>

                <!-- Jenis Kelamin -->
                <div class="flex flex-col gap-2">
                    <label class="text-[0.75rem] font-black text-text-muted uppercase tracking-wider pl-1" for="jenis_kelamin">Jenis Kelamin <span class="text-rose-500">*</span></label>
                    <select id="jenis_kelamin" name="jenis_kelamin" class="<?= $cls('jenis_kelamin') ?>">
                        <option value="">-- Pilih Jenis Kelamin --</option>
                        <option value="L" <?= $old('jenis_kelamin') === 'L' ? 'selected' : '' ?>>Laki-laki</option>
                        <option value="P" <?= $old('jenis_kelamin') === 'P' ? 'selected' : '' ?>>Perempuan</option>
                    </select>
                    <?php if ($e = $err('jenis_kelamin')): ?>
                        <span class="text-[0.7rem] text-rose-600 font-bold pl-1"><?= e($e) ?></span>
                    <?php endif; ?>
                </div>

                <!-- Jurusan -->
                <div class="flex flex-col gap-2">
                    <label class="text-[0.75rem] font-black text-text-muted uppercase tracking-wider pl-1" for="jurusan">Jurusan (Opsional)</label>
                    <input type="text" id="jurusan" name="jurusan"
                           class="<?= $cls('jurusan') ?>"
                           value="<?= e($old('jurusan')) ?>"
                           placeholder="Contoh: IPA, IPS, RPL">
                    <?php if ($e = $err('jurusan')): ?>
                        <span class="text-[0.7rem] text-rose-600 font-bold pl-1"><?= e($e) ?></span>
                    <?php endif; ?>
                </div>

                <!-- Tahun Lulus -->
                <div class="flex flex-col gap-2">
                    <label class="text-[0.75rem] font-black text-text-muted uppercase tracking-wider pl-1" for="tahun_lulus">Tahun Lulus <span class="text-rose-500">*</span></label>
                    <input type="number" id="tahun_lulus" name="tahun_lulus"
                           class="<?= $cls('tahun_lulus') ?>"
                           value="<?= e($old('tahun_lulus') ?: date('Y')) ?>"
                           min="2000" max="<?= date('Y') + 1 ?>">
                    <?php if ($e = $err('tahun_lulus')): ?>
                        <span class="text-[0.7rem] text-rose-600 font-bold pl-1"><?= e($e) ?></span>
                    <?php endif; ?>
                </div>

                <!-- Status Kelulusan -->
                <div class="flex flex-col gap-2">
                    <label class="text-[0.75rem] font-black text-text-muted uppercase tracking-wider pl-1" for="status_kelulusan">Status Kelulusan <span class="text-rose-500">*</span></label>
                    <select id="status_kelulusan" name="status_kelulusan"
                            class="<?= $cls('status_kelulusan') ?>">
                        <option value="">-- Pilih Status --</option>
                        <option value="lulus"       <?= $old('status_kelulusan') === 'lulus'       ? 'selected' : '' ?>>LULUS</option>
                        <option value="tidak_lulus" <?= $old('status_kelulusan') === 'tidak_lulus' ? 'selected' : '' ?>>TIDAK LULUS</option>
                    </select>
                    <?php if ($e = $err('status_kelulusan')): ?>
                        <span class="text-[0.7rem] text-rose-600 font-bold pl-1"><?= e($e) ?></span>
                    <?php endif; ?>
                </div>

                <!-- Nilai Rata-rata -->
                <div class="flex flex-col gap-2">
                    <label class="text-[0.75rem] font-black text-text-muted uppercase tracking-wider pl-1" for="nilai_rata_rata">Nilai Rata-rata <span class="text-rose-500">*</span></label>
                    <input type="number" id="nilai_rata_rata" name="nilai_rata_rata"
                           class="<?= $cls('nilai_rata_rata') ?>"
                           value="<?= e($old('nilai_rata_rata')) ?>"
                           step="0.01" min="0" max="100"
                           placeholder="0.00">
                    <?php if ($e = $err('nilai_rata_rata')): ?>
                        <span class="text-[0.7rem] text-rose-600 font-bold pl-1"><?= e($e) ?></span>
                    <?php endif; ?>
                </div>

                <!-- Keterangan -->
                <div class="flex flex-col gap-2 md:col-span-2">
                    <label class="text-[0.75rem] font-black text-text-muted uppercase tracking-wider pl-1" for="keterangan">Keterangan Tambahan</label>
                    <textarea id="keterangan" name="keterangan"
                               class="<?= $input_base ?>"
                               rows="3" placeholder="Informasi tambahan jika ada..."><?= e($old('keterangan')) ?></textarea>
                </div>

            </div><!-- /.grid -->

            <div class="flex items-center justify-end gap-3 pt-6 border-t border-border">
                <a href="/admin/siswa" class="text-text-muted hover:text-text font-bold text-sm px-4 transition-colors">Batal</a>
                <button type="submit" class="bg-primary text-white font-black py-3 px-10 rounded-xl hover:opacity-90 hover:shadow-lg hover:shadow-primary/20 transition-all cursor-pointer">
                    <?= isset($siswa) ? 'Simpan Perubahan' : 'Tambah Siswa' ?>
                </button>
            </div>
        </form>
    </div>
</div>
