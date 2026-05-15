<div class="max-w-2xl mx-auto">
    <div class="mb-6 flex items-center justify-between">
        <h2 class="text-xl font-bold text-text">👤 Profil Saya</h2>
        <span class="text-xs font-bold text-text-muted bg-slate-100 px-3 py-1 rounded-full uppercase tracking-widest"><?= e($user['role']) ?></span>
    </div>

    <?php if (flash('success')): ?>
    <div class="mb-6 p-4 bg-emerald-50 border border-emerald-100 text-emerald-700 text-sm rounded-xl animate-fade-in">
        <?= flash('success') ?>
    </div>
    <?php endif; ?>

    <div class="bg-surface border border-border rounded-2xl shadow-sm overflow-hidden">
        <form action="/admin/my/profile" method="POST" class="p-8">
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
                        <p class="mt-1.5 text-[0.65rem] text-rose-500 font-bold uppercase italic">* Mengubah ini akan memaksa logout</p>
                    </div>

                    <!-- Email -->
                    <div>
                        <label class="block text-[0.7rem] font-black uppercase tracking-wider text-text-muted mb-2">Email</label>
                        <input type="email" name="email" value="<?= e(old('email', $user['email'])) ?>" required
                            class="w-full bg-white border border-border rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-primary transition-all">
                        <p class="mt-1.5 text-[0.65rem] text-rose-500 font-bold uppercase italic">* Mengubah ini akan memaksa logout</p>
                    </div>
                </div>

                <!-- Password -->
                <div>
                    <label class="block text-[0.7rem] font-black uppercase tracking-wider text-text-muted mb-2">Password Baru (Kosongkan jika tidak diubah)</label>
                    <input type="password" name="password"
                        class="w-full bg-white border border-border rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-primary transition-all"
                        placeholder="••••••••">
                    <p class="mt-1.5 text-[0.65rem] text-rose-500 font-bold uppercase italic">* Mengisi ini akan memaksa logout</p>
                </div>
            </div>

            <div class="mt-8 pt-6 border-t border-border flex justify-end items-center gap-4">
                <p class="text-[0.7rem] text-text-muted italic">Perubahan data keamanan memerlukan login ulang.</p>
                <button type="submit" class="bg-primary text-white font-bold py-3 px-8 rounded-xl hover:opacity-90 transition-all shadow-lg shadow-primary/20">
                    Simpan Profil
                </button>
            </div>
        </form>
    </div>
</div>
