<!-- Dashboard Filter -->
<div class="bg-surface border border-border rounded-2xl p-6 mb-8 shadow-sm">
    <form method="GET" action="/admin/dashboard" class="flex flex-col md:flex-row items-end gap-4">
        <div class="w-full md:w-64">
            <label class="block text-[0.7rem] font-black uppercase tracking-wider text-text-muted mb-2">Filter Berdasarkan Tahun</label>
            <select name="tahun" onchange="this.form.submit()" class="w-full bg-white border border-border rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-primary transition-all cursor-pointer">
                <option value="">-- Semua Tahun --</option>
                <?php foreach ($years as $year): ?>
                    <option value="<?= $year ?>" <?= (int)$filterTahun === (int)$year ? 'selected' : '' ?>>Tahun <?= $year ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="flex-1">
            <?php if ($filterTahun): ?>
                <div class="flex items-center gap-2 text-sm text-text-muted">
                    <span class="inline-block w-2 h-2 bg-primary rounded-full"></span>
                    Menampilkan rekapitulasi untuk tahun <strong class="text-text"><?= e($filterTahun) ?></strong>
                    <a href="/admin/dashboard" class="ml-2 text-primary hover:underline text-xs font-bold uppercase">Reset Filter</a>
                </div>
            <?php else: ?>
                <div class="text-sm text-text-muted">Menampilkan rekapitulasi keseluruhan tahun.</div>
            <?php endif; ?>
        </div>
    </form>
</div>

<!-- Stats Cards -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-surface border border-border rounded-2xl p-6 flex items-center gap-5 hover:shadow-md transition-all">
        <div class="w-14 h-14 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center text-2xl shrink-0">👥</div>
        <div>
            <div class="text-2xl font-black text-text leading-none"><?= number_format($totalSiswa) ?></div>
            <div class="text-text-muted text-sm mt-1">Total Siswa</div>
        </div>
    </div>
    <div class="bg-surface border border-border rounded-2xl p-6 flex items-center gap-5 hover:shadow-md transition-all">
        <div class="w-14 h-14 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center text-2xl shrink-0">✅</div>
        <div>
            <div class="text-2xl font-black text-text leading-none"><?= number_format($totalLulus) ?></div>
            <div class="text-text-muted text-sm mt-1">Siswa Lulus</div>
        </div>
    </div>
    <div class="bg-surface border border-border rounded-2xl p-6 flex items-center gap-5 hover:shadow-md transition-all">
        <div class="w-14 h-14 bg-rose-50 text-rose-600 rounded-xl flex items-center justify-center text-2xl shrink-0">❌</div>
        <div>
            <div class="text-2xl font-black text-text leading-none"><?= number_format($totalTidak) ?></div>
            <div class="text-text-muted text-sm mt-1">Tidak Lulus</div>
        </div>
    </div>
    <div class="bg-surface border border-border rounded-2xl p-6 flex items-center gap-5 hover:shadow-md transition-all">
        <div class="w-14 h-14 bg-purple-50 text-purple-600 rounded-xl flex items-center justify-center text-2xl shrink-0">📈</div>
        <div>
            <div class="text-2xl font-black text-text leading-none"><?= $persentase ?>%</div>
            <div class="text-text-muted text-sm mt-1">Tingkat Kelulusan</div>
        </div>
    </div>
</div>

<!-- Statistik Table -->
<div class="bg-surface border border-border rounded-2xl overflow-hidden mt-8 shadow-sm">
    <div class="flex items-center justify-between p-6 border-b border-border flex-wrap gap-4">
        <h2 class="text-lg font-bold">📊 Rekap Kelulusan per Tahun</h2>
        <a href="/admin/siswa/create" class="bg-primary text-white text-sm font-bold py-2 px-5 rounded-lg hover:opacity-90 transition-all shadow-md shadow-primary/10">+ Tambah Siswa</a>
    </div>
    <div class="p-6">
        <?php if (empty($statistik)): ?>
            <p class="text-text-muted text-center py-12">Belum ada data kelulusan.</p>
        <?php else: ?>
        <div class="overflow-x-auto">
            <table class="w-full border-collapse text-sm">
                <thead>
                    <tr class="bg-slate-50">
                        <th class="px-4 py-3 text-left text-[0.7rem] font-bold text-text-muted uppercase tracking-wider border-b border-border">Tahun</th>
                        <th class="px-4 py-3 text-left text-[0.7rem] font-bold text-text-muted uppercase tracking-wider border-b border-border">Total Siswa</th>
                        <th class="px-4 py-3 text-left text-[0.7rem] font-bold text-text-muted uppercase tracking-wider border-b border-border">Lulus</th>
                        <th class="px-4 py-3 text-left text-[0.7rem] font-bold text-text-muted uppercase tracking-wider border-b border-border">Tidak Lulus</th>
                        <th class="px-4 py-3 text-left text-[0.7rem] font-bold text-text-muted uppercase tracking-wider border-b border-border">Rata-rata Nilai</th>
                        <th class="px-4 py-3 text-left text-[0.7rem] font-bold text-text-muted uppercase tracking-wider border-b border-border">% Kelulusan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    <?php foreach ($statistik as $row): ?>
                        <?php $persen = $row['total'] > 0
                            ? round(($row['lulus'] / $row['total']) * 100, 1) : 0; ?>
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-4 py-4 font-bold text-text"><?= e($row['tahun_lulus']) ?></td>
                            <td class="px-4 py-4 text-text-muted font-medium"><?= number_format($row['total']) ?></td>
                            <td class="px-4 py-4"><span class="px-3 py-1 bg-emerald-50 text-emerald-700 rounded-full text-xs font-bold"><?= number_format($row['lulus']) ?></span></td>
                            <td class="px-4 py-4"><span class="px-3 py-1 bg-rose-50 text-rose-700 rounded-full text-xs font-bold"><?= number_format($row['tidak_lulus']) ?></span></td>
                            <td class="px-4 py-4 font-mono font-bold text-primary"><?= number_format((float)$row['rata_nilai'], 2) ?></td>
                            <td class="px-4 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="flex-1 h-2 bg-primary/30 rounded-full overflow-hidden">
                                        <div class="h-full bg-primary transition-all" style="width:<?= $persen ?>%"></div>
                                    </div>
                                    <span class="text-xs font-black text-text-muted shrink-0"><?= $persen ?>%</span>
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
