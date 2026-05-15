<?php
function sort_link($col, $currentSort, $currentOrder) {
    $params = $_GET;
    $params['sort'] = $col;
    $params['order'] = ($col === $currentSort && $currentOrder === 'ASC') ? 'DESC' : 'ASC';
    return '/admin/siswa?' . http_build_query($params);
}

function sort_icon($col, $currentSort, $currentOrder) {
    if ($col !== $currentSort) return '↕️';
    return $currentOrder === 'ASC' ? '🔼' : '🔽';
}
?>
<div class="bg-surface border border-border rounded-2xl overflow-hidden shadow-sm animate-fade-in">
    <div class="flex items-center justify-between p-6 border-b border-border flex-wrap gap-4">
        <h2 class="text-lg font-bold">👥 Data Siswa</h2>
        <div class="flex gap-2">
            <a href="/admin/siswa/import" class="bg-white border border-border text-text text-sm font-bold py-2 px-4 rounded-lg hover:bg-slate-50 transition-all flex items-center gap-2">📥 Import</a>
            <a href="/admin/siswa/create" class="bg-primary text-white text-sm font-bold py-2 px-4 rounded-lg hover:opacity-90 transition-all shadow-md shadow-primary/10">+ Tambah</a>
        </div>
    </div>

    <!-- Filters -->
    <div class="p-6 bg-slate-50/50 border-b border-border">
        <form method="GET" action="/admin/siswa" class="flex flex-col lg:flex-row w-full gap-4">
            <div class="md:col-span-1">
                <input type="text" name="search" value="<?= e($search) ?>" placeholder="Cari nama atau NISN..." class="w-full bg-white border border-border rounded-xl px-4 py-2 text-sm focus:outline-none focus:border-primary transition-all">
            </div>
            <div>
                <select name="tahun" class="w-full bg-white border border-border rounded-xl px-4 py-2 text-sm focus:outline-none focus:border-primary transition-all">
                    <option value="">-- Semua Tahun --</option>
                    <?php for($y = date('Y'); $y >= 2020; $y--): ?>
                        <option value="<?= $y ?>" <?= $filterTahun == $y ? 'selected' : '' ?>><?= $y ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div>
                <select name="status" class="w-full bg-white border border-border rounded-xl px-4 py-2 text-sm focus:outline-none focus:border-primary transition-all">
                    <option value="">-- Semua Status --</option>
                    <option value="lulus" <?= $filterStatus === 'lulus' ? 'selected' : '' ?>>Lulus</option>
                    <option value="tidak_lulus" <?= $filterStatus === 'tidak_lulus' ? 'selected' : '' ?>>Tidak Lulus</option>
                </select>
            </div>
            <div>
                <select name="limit" class="w-full bg-white border border-border rounded-xl px-4 py-2 text-sm focus:outline-none focus:border-primary transition-all">
                    <?php foreach([10, 15, 25, 50, 100] as $l): ?>
                        <option value="<?= $l ?>" <?= $limit == $l ? 'selected' : '' ?>>Show <?= $l ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="bg-primary text-white font-bold py-2 px-4 rounded-xl hover:opacity-90 transition-all cursor-pointer">Filter</button>
            <a href="/admin/siswa" class="bg-white border border-border rounded-xl px-4 py-2 text-sm hover:border-primary focus:outline-none focus:border-primary transition-all">Reset</a>
        </form>
    </div>

    <div class="p-6">
        <?php if (empty($siswa)): ?>
            <div class="text-center py-12">
                <div class="text-5xl mb-4">Empty</div>
                <p class="text-text-muted">Data siswa tidak ditemukan.</p>
            </div>
        <?php else: ?>
        <!-- Bulk Action Bar -->
        <div id="bulk-action-bar" class="hidden mb-6 p-4 bg-primary/5 border border-primary/20 rounded-xl flex items-center justify-between animate-in fade-in slide-in-from-top-4 duration-300">
            <div class="flex items-center gap-3">
                <span class="text-sm font-bold text-primary"><span id="selected-count">0</span> Siswa Terpilih</span>
                <div class="h-4 w-px bg-primary/20"></div>
                <p class="text-[0.7rem] text-text-muted font-medium uppercase tracking-wider">Ubah Status Menjadi:</p>
            </div>
            <div class="flex gap-2">
                <button type="button" onclick="submitBulk('lulus')" class="bg-emerald-500 text-white text-[0.7rem] font-black uppercase tracking-wider py-2 px-4 rounded-lg hover:bg-emerald-600 transition-all cursor-pointer">Lulus</button>
                <button type="button" onclick="submitBulk('tidak_lulus')" class="bg-rose-500 text-white text-[0.7rem] font-black uppercase tracking-wider py-2 px-4 rounded-lg hover:bg-rose-600 transition-all cursor-pointer">Tidak Lulus</button>
                <button type="button" onclick="resetSelection()" class="text-[0.7rem] font-bold text-text-muted hover:text-text px-2 transition-all cursor-pointer">Batal</button>
            </div>
        </div>

        <form id="bulk-form" method="POST" action="/admin/siswa/bulk-update">
            <?= csrf_field() ?>
            <input type="hidden" name="status" id="bulk-status-input">
            <div class="overflow-x-auto">
                <table class="w-full border-collapse text-sm">
                    <thead>
                        <tr class="bg-slate-50">
                            <th class="w-10 px-4 py-3 border-b border-border">
                                <input type="checkbox" id="select-all" class="w-4 h-4 rounded border-border text-primary focus:ring-primary cursor-pointer">
                            </th>
                            <th class="px-4 py-3 text-left text-[0.7rem] font-bold text-text-muted uppercase tracking-wider border-b border-border">
                                <a href="<?= sort_link('nama', $sort, $order) ?>" class="flex items-center gap-1 hover:text-primary transition-colors">
                                    Siswa <?= sort_icon('nama', $sort, $order) ?>
                                </a>
                            </th>
                            <th class="px-4 py-3 text-left text-[0.7rem] font-bold text-text-muted uppercase tracking-wider border-b border-border">
                                <a href="<?= sort_link('tahun_lulus', $sort, $order) ?>" class="flex items-center gap-1 hover:text-primary transition-colors">
                                    Tahun Lulus <?= sort_icon('tahun_lulus', $sort, $order) ?>
                                </a>
                            </th>
                            <th class="px-4 py-3 text-left text-[0.7rem] font-bold text-text-muted uppercase tracking-wider border-b border-border">
                                <a href="<?= sort_link('jurusan', $sort, $order) ?>" class="flex items-center gap-1 hover:text-primary transition-colors">
                                    Jurusan <?= sort_icon('jurusan', $sort, $order) ?>
                                </a>
                            </th>
                            <th class="px-4 py-3 text-left text-[0.7rem] font-bold text-text-muted uppercase tracking-wider border-b border-border">
                                <a href="<?= sort_link('status_kelulusan', $sort, $order) ?>" class="flex items-center gap-1 hover:text-primary transition-colors">
                                    Status <?= sort_icon('status_kelulusan', $sort, $order) ?>
                                </a>
                            </th>
                            <th class="px-4 py-3 text-left text-[0.7rem] font-bold text-text-muted uppercase tracking-wider border-b border-border">
                                <a href="<?= sort_link('nilai_rata_rata', $sort, $order) ?>" class="flex items-center gap-1 hover:text-primary transition-colors">
                                    Rata-rata <?= sort_icon('nilai_rata_rata', $sort, $order) ?>
                                </a>
                            </th>
                            <th class="px-4 py-3 text-center text-[0.7rem] font-bold text-text-muted uppercase tracking-wider border-b border-border">Aksi</th>
                        </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    <?php foreach ($siswa as $s): ?>
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-4 py-4 text-center">
                            <input type="checkbox" name="ids[]" value="<?= e($s['id']) ?>" class="siswa-checkbox w-4 h-4 rounded border-border text-primary focus:ring-primary cursor-pointer">
                        </td>
                        <td class="px-4 py-4">
                            <div class="flex flex-col">
                                <span class="font-extrabold text-text"><?= e($s['nama']) ?></span>
                                <span class="text-[0.75rem] text-text-muted font-medium"><?= e($s['nisn']) ?></span>
                            </div>
                        </td>
                        <td class="px-4 py-4">
                            <span class="text-primary font-medium"><?= e($s['tahun_lulus'] ?: '-') ?></span>
                        </td>
                        <td class="px-4 py-4">
                            <span class="text-text-muted font-medium italic"><?= e($s['jurusan'] ?: '-') ?></span>
                        </td>
                        <td class="px-4 py-4">
                            <?php if ($s['status_kelulusan'] === 'lulus'): ?>
                                <span class="px-2.5 py-1 bg-emerald-50 text-emerald-700 rounded-full text-[0.7rem] font-black uppercase tracking-wider">Lulus</span>
                            <?php else: ?>
                                <span class="px-2.5 py-1 bg-rose-50 text-rose-700 rounded-full text-[0.7rem] font-black uppercase tracking-wider">Tidak Lulus</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-4 py-4">
                            <span class="font-bold text-primary"><?= number_format((float)$s['nilai_rata_rata'], 2) ?></span>
                        </td>
                        <td class="px-4 py-4">
                            <div class="flex items-center justify-center gap-2">
                                <a href="/admin/siswa/<?= e($s['id']) ?>/edit" class="w-8 h-8 flex items-center justify-center bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition-all" title="Edit">✏️</a>
                                <form method="POST" action="/admin/siswa/<?= e($s['id']) ?>/delete" onsubmit="return confirm('Hapus data siswa ini?')">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="w-8 h-8 flex items-center justify-center bg-rose-50 text-rose-600 rounded-lg hover:bg-rose-100 transition-all cursor-pointer" title="Hapus">🗑</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                </table>
            </div>
        </form>

        <!-- Pagination -->
        <div class="flex flex-wrap items-center justify-between gap-4 mt-8 border-t border-border pt-6">
            <p class="text-[0.75rem] font-bold text-text-muted uppercase tracking-wider">
                Menampilkan <span class="text-text"><?= count($siswa) ?></span> dari <span class="text-text"><?= number_format($pagination['total']) ?></span> data
            </p>
            <?= pagination($pagination, '/admin/siswa?' . http_build_query(array_filter([
                'search' => $search,
                'tahun'  => $filterTahun,
                'status' => $filterStatus,
                'limit'  => $limit,
                'sort'   => $sort,
                'order'  => $order,
            ]))) ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
    const selectAll = document.getElementById('select-all');
    const checkboxes = document.querySelectorAll('.siswa-checkbox');
    const bulkBar = document.getElementById('bulk-action-bar');
    const selectedCount = document.getElementById('selected-count');
    const bulkForm = document.getElementById('bulk-form');
    const bulkStatusInput = document.getElementById('bulk-status-input');

    function updateBulkBar() {
        const checked = document.querySelectorAll('.siswa-checkbox:checked').length;
        selectedCount.textContent = checked;
        
        if (checked > 0) {
            bulkBar.classList.remove('hidden');
        } else {
            bulkBar.classList.add('hidden');
            selectAll.checked = false;
        }
    }

    selectAll.addEventListener('change', function() {
        checkboxes.forEach(cb => {
            cb.checked = selectAll.checked;
        });
        updateBulkBar();
    });

    checkboxes.forEach(cb => {
        cb.addEventListener('change', function() {
            if (!this.checked) {
                selectAll.checked = false;
            } else {
                const allChecked = Array.from(checkboxes).every(c => c.checked);
                selectAll.checked = allChecked;
            }
            updateBulkBar();
        });
    });

    function resetSelection() {
        checkboxes.forEach(cb => cb.checked = false);
        selectAll.checked = false;
        updateBulkBar();
    }

    function submitBulk(status) {
        if (confirm(`Ubah status ${document.querySelectorAll('.siswa-checkbox:checked').length} siswa menjadi ${status.replace('_', ' ')}?`)) {
            bulkStatusInput.value = status;
            bulkForm.submit();
        }
    }
</script>
