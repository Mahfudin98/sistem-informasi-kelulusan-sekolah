<div class="card">
    <div class="card-header">
        <h2 class="card-title">👥 Data Siswa</h2>
        <div style="display: flex; gap: 0.5rem;">
            <a href="/admin/siswa/import" class="btn btn-secondary btn-sm">📥 Import Excel</a>
            <a href="/admin/siswa/create" class="btn btn-primary btn-sm">+ Tambah Siswa</a>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="card-filter">
        <form method="GET" action="/admin/siswa" class="filter-form" id="filterForm">
            <input
                type="text"
                name="search"
                class="form-input"
                placeholder="Cari nama atau NISN…"
                value="<?= e($search) ?>"
            >
            <select name="tahun" class="form-select">
                <option value="">Semua Tahun</option>
                <?php foreach ($years as $y): ?>
                    <option value="<?= e($y['tahun_lulus']) ?>"
                        <?= $filterTahun == $y['tahun_lulus'] ? 'selected' : '' ?>>
                        <?= e($y['tahun_lulus']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <select name="status" class="form-select">
                <option value="">Semua Status</option>
                <option value="lulus"       <?= $filterStatus === 'lulus'       ? 'selected' : '' ?>>Lulus</option>
                <option value="tidak_lulus" <?= $filterStatus === 'tidak_lulus' ? 'selected' : '' ?>>Tidak Lulus</option>
            </select>
            <button type="submit" class="btn btn-secondary btn-sm">Filter</button>
            <a href="/admin/siswa" class="btn btn-ghost btn-sm">Reset</a>
        </form>
    </div>

    <div class="card-body">
        <?php if (empty($siswa)): ?>
            <p class="text-muted text-center py-8">Tidak ada data siswa yang ditemukan.</p>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table" id="siswaTable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>NISN</th>
                        <th>Nama</th>
                        <th>Jurusan</th>
                        <th>Tahun</th>
                        <th>Status</th>
                        <th>Nilai</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($siswa as $i => $s): ?>
                    <tr>
                        <td><?= (($pagination['page'] - 1) * $pagination['perPage']) + $i + 1 ?></td>
                        <td><code><?= e($s['nisn']) ?></code></td>
                        <td><?= e($s['nama']) ?></td>
                        <td><?= e($s['jurusan']) ?></td>
                        <td><?= e($s['tahun_lulus']) ?></td>
                        <td>
                            <?php if ($s['status_kelulusan'] === 'lulus'): ?>
                                <span class="badge badge-success">Lulus</span>
                            <?php else: ?>
                                <span class="badge badge-danger">Tidak Lulus</span>
                            <?php endif; ?>
                        </td>
                        <td><?= number_format((float)$s['nilai_rata_rata'], 2) ?></td>
                        <td class="action-cell">
                            <a href="/admin/siswa/<?= $s['id'] ?>"
                               class="btn btn-ghost btn-xs" title="Detail">👁</a>
                            <a href="/admin/siswa/<?= $s['id'] ?>/edit"
                               class="btn btn-secondary btn-xs" title="Edit">✏️</a>
                            <form method="POST"
                                  action="/admin/siswa/<?= $s['id'] ?>/delete"
                                  class="form-delete"
                                  data-name="<?= e($s['nama']) ?>"
                                  style="display:inline">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-danger btn-xs" title="Hapus">🗑</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="pagination-wrap">
            <p class="pagination-info">
                Menampilkan <?= count($siswa) ?> dari <?= number_format($pagination['total']) ?> data
            </p>
            <?= pagination($pagination, '/admin/siswa?' . http_build_query(array_filter([
                'search' => $search,
                'tahun'  => $filterTahun,
                'status' => $filterStatus,
            ]))) ?>
        </div>
        <?php endif; ?>
    </div>
</div>
