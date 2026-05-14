<!-- Stats Cards -->
<div class="stats-grid">
    <div class="stat-card stat-blue">
        <div class="stat-icon">👥</div>
        <div class="stat-body">
            <div class="stat-value"><?= number_format($totalSiswa) ?></div>
            <div class="stat-label">Total Siswa</div>
        </div>
    </div>
    <div class="stat-card stat-green">
        <div class="stat-icon">✅</div>
        <div class="stat-body">
            <div class="stat-value"><?= number_format($totalLulus) ?></div>
            <div class="stat-label">Siswa Lulus</div>
        </div>
    </div>
    <div class="stat-card stat-red">
        <div class="stat-icon">❌</div>
        <div class="stat-body">
            <div class="stat-value"><?= number_format($totalTidak) ?></div>
            <div class="stat-label">Tidak Lulus</div>
        </div>
    </div>
    <div class="stat-card stat-purple">
        <div class="stat-icon">📈</div>
        <div class="stat-body">
            <div class="stat-value"><?= $persentase ?>%</div>
            <div class="stat-label">Tingkat Kelulusan</div>
        </div>
    </div>
</div>

<!-- Statistik Table -->
<div class="card mt-6">
    <div class="card-header">
        <h2 class="card-title">📊 Rekap Kelulusan per Tahun</h2>
        <a href="/admin/siswa/create" class="btn btn-primary btn-sm">+ Tambah Siswa</a>
    </div>
    <div class="card-body">
        <?php if (empty($statistik)): ?>
            <p class="text-muted text-center py-8">Belum ada data kelulusan.</p>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Tahun</th>
                        <th>Total Siswa</th>
                        <th>Lulus</th>
                        <th>Tidak Lulus</th>
                        <th>Rata-rata Nilai</th>
                        <th>% Kelulusan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($statistik as $row): ?>
                        <?php $persen = $row['total'] > 0
                            ? round(($row['lulus'] / $row['total']) * 100, 1) : 0; ?>
                        <tr>
                            <td><strong><?= e($row['tahun_lulus']) ?></strong></td>
                            <td><?= number_format($row['total']) ?></td>
                            <td><span class="badge badge-success"><?= number_format($row['lulus']) ?></span></td>
                            <td><span class="badge badge-danger"><?= number_format($row['tidak_lulus']) ?></span></td>
                            <td><?= number_format((float)$row['rata_nilai'], 2) ?></td>
                            <td>
                                <div class="progress-bar-wrap">
                                    <div class="progress-bar" style="width:<?= $persen ?>%"></div>
                                    <span><?= $persen ?>%</span>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>
