<div class="bg-surface border border-border rounded-2xl overflow-hidden shadow-sm animate-fade-in">
    <div class="flex items-center justify-between p-6 border-b border-border flex-wrap gap-4">
        <h2 class="text-lg font-bold">📜 Log Audit Sistem</h2>
        <div class="text-xs text-text-muted italic">Mencatat aktivitas perubahan data oleh administrator.</div>
    </div>

    <div class="p-6">
        <div class="overflow-x-auto">
            <table class="w-full border-collapse text-sm">
                <thead>
                    <tr class="bg-slate-50">
                        <th class="px-4 py-3 text-left text-[0.7rem] font-bold text-text-muted uppercase tracking-wider border-b border-border">Waktu</th>
                        <th class="px-4 py-3 text-left text-[0.7rem] font-bold text-text-muted uppercase tracking-wider border-b border-border">Admin</th>
                        <th class="px-4 py-3 text-left text-[0.7rem] font-bold text-text-muted uppercase tracking-wider border-b border-border">Aksi</th>
                        <th class="px-4 py-3 text-left text-[0.7rem] font-bold text-text-muted uppercase tracking-wider border-b border-border">Entitas</th>
                        <th class="px-4 py-3 text-left text-[0.7rem] font-bold text-text-muted uppercase tracking-wider border-b border-border">Keterangan</th>
                        <th class="px-4 py-3 text-left text-[0.7rem] font-bold text-text-muted uppercase tracking-wider border-b border-border">IP Address</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    <?php if (empty($logs)): ?>
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center text-text-muted italic">Belum ada log yang tercatat.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($logs as $log): ?>
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-4 py-4 whitespace-nowrap text-text-muted font-medium">
                                <?= date('d/m/Y H:i', strtotime($log['created_at'])) ?>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                <span class="font-extrabold text-text">@<?= e($log['username']) ?></span>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                <?php
                                    $actionClass = match($log['action']) {
                                        'create' => 'bg-emerald-50 text-emerald-600',
                                        'update' => 'bg-blue-50 text-blue-600',
                                        'delete' => 'bg-rose-50 text-rose-600',
                                        'bulk_update' => 'bg-purple-50 text-purple-600',
                                        default => 'bg-slate-50 text-slate-600'
                                    };
                                ?>
                                <span class="px-2 py-0.5 rounded-md text-[0.65rem] font-black uppercase tracking-wider <?= $actionClass ?>">
                                    <?= str_replace('_', ' ', $log['action']) ?>
                                </span>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                <span class="text-[0.7rem] font-bold text-text-muted uppercase"><?= e($log['entity']) ?> #<?= $log['entity_id'] ?: '-' ?></span>
                            </td>
                            <td class="px-4 py-4 min-w-[200px]">
                                <div class="text-text font-medium leading-relaxed"><?= e($log['description']) ?></div>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-text-muted font-mono text-[0.7rem]">
                                <?= e($log['ip_address'] ?: '-') ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if ($pagination['lastPage'] > 1): ?>
        <div class="mt-8">
            <?= pagination($pagination, '/admin/audit-logs?page=') ?>
        </div>
        <?php endif; ?>
    </div>
</div>
