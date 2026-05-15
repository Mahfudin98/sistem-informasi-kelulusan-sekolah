<div class="max-w-2xl mx-auto">
    <div class="mb-6 flex items-center justify-between">
        <h2 class="text-xl font-bold text-text">✏️ Edit Admin: <?= e($user['name']) ?></h2>
        <a href="/admin/users" class="text-sm font-bold text-text-muted hover:text-text transition-all">← Kembali</a>
    </div>

    <div class="bg-surface border border-border rounded-2xl shadow-sm overflow-hidden">
        <form action="/admin/users/<?= e($user['id']) ?>/update" method="POST" class="p-8">
            <?= csrf_field() ?>

            <div class="grid gap-6">
                <!-- Name -->
                <div>
                    <label class="block text-[0.7rem] font-black uppercase tracking-wider text-text-muted mb-2">Nama Lengkap</label>
                    <input type="text" name="name" value="<?= e(old('name', $user['name'])) ?>" required
                        class="w-full bg-white border border-border rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-primary transition-all">
                </div>

                <div class="grid md:grid-cols-2 gap-6">
                    <!-- Username -->
                    <div>
                        <label class="block text-[0.7rem] font-black uppercase tracking-wider text-text-muted mb-2">Username</label>
                        <input type="text" name="username" value="<?= e(old('username', $user['username'])) ?>" required
                            class="w-full bg-white border border-border rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-primary transition-all">
                    </div>

                    <!-- Email -->
                    <div>
                        <label class="block text-[0.7rem] font-black uppercase tracking-wider text-text-muted mb-2">Email</label>
                        <input type="email" name="email" value="<?= e(old('email', $user['email'])) ?>" required
                            class="w-full bg-white border border-border rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-primary transition-all">
                    </div>
                </div>

                <div class="grid md:grid-cols-2 gap-6">
                    <!-- Password -->
                    <div>
                        <label class="block text-[0.7rem] font-black uppercase tracking-wider text-text-muted mb-2">Password Baru (Kosongkan jika tidak diubah)</label>
                        <input type="password" name="password"
                            class="w-full bg-white border border-border rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-primary transition-all"
                            placeholder="••••••••">
                    </div>

                    <!-- Role -->
                    <div>
                        <label class="block text-[0.7rem] font-black uppercase tracking-wider text-text-muted mb-2">Role</label>
                        <select name="role" required
                            class="w-full bg-white border border-border rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-primary transition-all">
                            <option value="admin" <?= old('role', $user['role']) === 'admin' ? 'selected' : '' ?>>Admin</option>
                            <?php if ($currentRole === 'superadmin'): ?>
                            <option value="superadmin" <?= old('role', $user['role']) === 'superadmin' ? 'selected' : '' ?>>Superadmin</option>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>
            </div>

            <div class="mt-8 pt-6 border-t border-border flex justify-end">
                <button type="submit" class="bg-primary text-white font-bold py-3 px-8 rounded-xl hover:opacity-90 transition-all shadow-lg shadow-primary/20">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
