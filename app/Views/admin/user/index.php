<div class="bg-surface border border-border rounded-2xl overflow-hidden shadow-sm animate-fade-in">
    <div class="flex items-center justify-between p-6 border-b border-border flex-wrap gap-4">
        <h2 class="text-lg font-bold">🔐 Manajemen Admin</h2>
        <a href="/admin/users/create" class="bg-primary text-white text-sm font-bold py-2 px-4 rounded-lg hover:opacity-90 transition-all shadow-md shadow-primary/10">+ Tambah Admin</a>
    </div>

    <div class="p-6">
        <div class="overflow-x-auto">
            <table class="w-full border-collapse text-sm">
                <thead>
                    <tr class="bg-slate-50">
                        <th class="px-4 py-3 text-left text-[0.7rem] font-bold text-text-muted uppercase tracking-wider border-b border-border">Nama</th>
                        <th class="px-4 py-3 text-left text-[0.7rem] font-bold text-text-muted uppercase tracking-wider border-b border-border">Username</th>
                        <th class="px-4 py-3 text-left text-[0.7rem] font-bold text-text-muted uppercase tracking-wider border-b border-border">Email</th>
                        <th class="px-4 py-3 text-left text-[0.7rem] font-bold text-text-muted uppercase tracking-wider border-b border-border">Role</th>
                        <th class="px-4 py-3 text-center text-[0.7rem] font-bold text-text-muted uppercase tracking-wider border-b border-border">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    <?php foreach ($users as $u): ?>
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-4 py-4">
                            <span class="font-extrabold text-text"><?= e($u['name']) ?></span>
                        </td>
                        <td class="px-4 py-4 font-medium text-text-muted">
                            @<?= e($u['username']) ?>
                        </td>
                        <td class="px-4 py-4 text-primary font-medium">
                            <?= e($u['email']) ?>
                        </td>
                        <td class="px-4 py-4">
                            <span class="px-2.5 py-1 bg-slate-100 text-slate-600 rounded-full text-[0.7rem] font-black uppercase tracking-wider">
                                <?= e($u['role']) ?>
                            </span>
                        </td>
                        <td class="px-4 py-4">
                            <div class="flex items-center justify-center gap-2">
                                <a href="/admin/users/<?= e($u['id']) ?>/edit" class="w-8 h-8 flex items-center justify-center bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition-all" title="Edit">✏️</a>
                                <?php if ((int)$u['id'] !== (int)auth()->id()): ?>
                                <form method="POST" action="/admin/users/<?= e($u['id']) ?>/delete" onsubmit="return confirm('Hapus admin ini?')">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="w-8 h-8 flex items-center justify-center bg-rose-50 text-rose-600 rounded-lg hover:bg-rose-100 transition-all cursor-pointer" title="Hapus">🗑</button>
                                </form>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
