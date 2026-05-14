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
$cls    = fn(string $k) => $err($k) ? 'form-input is-invalid' : 'form-input';
?>

<div class="card">
    <div class="card-header">
        <h2 class="card-title">
            <?= isset($siswa) ? '✏️ Edit Data Siswa' : '➕ Tambah Siswa Baru' ?>
        </h2>
        <a href="/admin/siswa" class="btn btn-ghost btn-sm">← Kembali</a>
    </div>
    <div class="card-body">
        <form method="POST" action="<?= e($action) ?>" id="siswaForm" novalidate>
            <?= csrf_field() ?>

            <div class="form-grid">

                <!-- NISN -->
                <div class="form-group">
                    <label class="form-label" for="nisn">NISN <span class="required">*</span></label>
                    <input type="text" id="nisn" name="nisn"
                           class="<?= $cls('nisn') ?>"
                           value="<?= e($old('nisn')) ?>"
                           maxlength="10" inputmode="numeric"
                           placeholder="10 digit NISN">
                    <?php if ($e = $err('nisn')): ?>
                        <span class="form-error"><?= e($e) ?></span>
                    <?php endif; ?>
                </div>

                <!-- Nama -->
                <div class="form-group form-group-wide">
                    <label class="form-label" for="nama">Nama Lengkap <span class="required">*</span></label>
                    <input type="text" id="nama" name="nama"
                           class="<?= $cls('nama') ?>"
                           value="<?= e($old('nama')) ?>"
                           placeholder="Nama lengkap siswa">
                    <?php if ($e = $err('nama')): ?>
                        <span class="form-error"><?= e($e) ?></span>
                    <?php endif; ?>
                </div>

                <!-- Tempat Lahir -->
                <div class="form-group">
                    <label class="form-label" for="tempat_lahir">Tempat Lahir <span class="required">*</span></label>
                    <input type="text" id="tempat_lahir" name="tempat_lahir"
                           class="<?= $cls('tempat_lahir') ?>"
                           value="<?= e($old('tempat_lahir')) ?>">
                    <?php if ($e = $err('tempat_lahir')): ?>
                        <span class="form-error"><?= e($e) ?></span>
                    <?php endif; ?>
                </div>

                <!-- Tanggal Lahir -->
                <div class="form-group">
                    <label class="form-label" for="tanggal_lahir">Tanggal Lahir <span class="required">*</span></label>
                    <input type="date" id="tanggal_lahir" name="tanggal_lahir"
                           class="<?= $cls('tanggal_lahir') ?>"
                           value="<?= e($old('tanggal_lahir')) ?>">
                    <?php if ($e = $err('tanggal_lahir')): ?>
                        <span class="form-error"><?= e($e) ?></span>
                    <?php endif; ?>
                </div>

                <!-- Jenis Kelamin -->
                <div class="form-group">
                    <label class="form-label" for="jenis_kelamin">Jenis Kelamin <span class="required">*</span></label>
                    <select id="jenis_kelamin" name="jenis_kelamin" class="form-select <?= $err('jenis_kelamin') ? 'is-invalid' : '' ?>">
                        <option value="">-- Pilih --</option>
                        <option value="L" <?= $old('jenis_kelamin') === 'L' ? 'selected' : '' ?>>Laki-laki</option>
                        <option value="P" <?= $old('jenis_kelamin') === 'P' ? 'selected' : '' ?>>Perempuan</option>
                    </select>
                    <?php if ($e = $err('jenis_kelamin')): ?>
                        <span class="form-error"><?= e($e) ?></span>
                    <?php endif; ?>
                </div>

                <!-- Jurusan -->
                <div class="form-group">
                    <label class="form-label" for="jurusan">Jurusan (Opsional)</label>
                    <input type="text" id="jurusan" name="jurusan"
                           class="<?= $cls('jurusan') ?>"
                           value="<?= e($old('jurusan')) ?>"
                           placeholder="Contoh: IPA, IPS, Rekayasa Perangkat Lunak">
                    <?php if ($e = $err('jurusan')): ?>
                        <span class="form-error"><?= e($e) ?></span>
                    <?php endif; ?>
                </div>

                <!-- Tahun Lulus -->
                <div class="form-group">
                    <label class="form-label" for="tahun_lulus">Tahun Lulus <span class="required">*</span></label>
                    <input type="number" id="tahun_lulus" name="tahun_lulus"
                           class="<?= $cls('tahun_lulus') ?>"
                           value="<?= e($old('tahun_lulus') ?: date('Y')) ?>"
                           min="2000" max="<?= date('Y') + 1 ?>">
                    <?php if ($e = $err('tahun_lulus')): ?>
                        <span class="form-error"><?= e($e) ?></span>
                    <?php endif; ?>
                </div>

                <!-- Status Kelulusan -->
                <div class="form-group">
                    <label class="form-label" for="status_kelulusan">Status Kelulusan <span class="required">*</span></label>
                    <select id="status_kelulusan" name="status_kelulusan"
                            class="form-select <?= $err('status_kelulusan') ? 'is-invalid' : '' ?>">
                        <option value="">-- Pilih --</option>
                        <option value="lulus"       <?= $old('status_kelulusan') === 'lulus'       ? 'selected' : '' ?>>Lulus</option>
                        <option value="tidak_lulus" <?= $old('status_kelulusan') === 'tidak_lulus' ? 'selected' : '' ?>>Tidak Lulus</option>
                    </select>
                    <?php if ($e = $err('status_kelulusan')): ?>
                        <span class="form-error"><?= e($e) ?></span>
                    <?php endif; ?>
                </div>

                <!-- Nilai Rata-rata -->
                <div class="form-group">
                    <label class="form-label" for="nilai_rata_rata">Nilai Rata-rata <span class="required">*</span></label>
                    <input type="number" id="nilai_rata_rata" name="nilai_rata_rata"
                           class="<?= $cls('nilai_rata_rata') ?>"
                           value="<?= e($old('nilai_rata_rata')) ?>"
                           step="0.01" min="0" max="100">
                    <?php if ($e = $err('nilai_rata_rata')): ?>
                        <span class="form-error"><?= e($e) ?></span>
                    <?php endif; ?>
                </div>

                <!-- Keterangan -->
                <div class="form-group form-group-wide">
                    <label class="form-label" for="keterangan">Keterangan</label>
                    <textarea id="keterangan" name="keterangan"
                              class="form-input"
                              rows="3"><?= e($old('keterangan')) ?></textarea>
                </div>

            </div><!-- /.form-grid -->

            <div class="form-actions">
                <a href="/admin/siswa" class="btn btn-ghost">Batal</a>
                <button type="submit" class="btn btn-primary">
                    <?= isset($siswa) ? 'Simpan Perubahan' : 'Tambah Siswa' ?>
                </button>
            </div>
        </form>
    </div>
</div>
