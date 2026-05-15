<div class="w-full max-w-[420px] animate-fade-in">
    <div class="bg-surface border border-border rounded-3xl p-10 shadow-lg">
        <div class="text-center mb-10">
            <div class="text-5xl mb-3">🛡️</div>
            <h1 class="text-2xl font-black tracking-tight">Atur Ulang Kata Sandi</h1>
            <p class="text-text-muted text-sm mt-1">Silakan masukkan kata sandi baru Anda</p>
        </div>

        <!-- Flash messages -->
        <?php if ($err = flash('error')): ?>
            <div class="bg-red-50 border border-red-200 text-red-700 p-4 rounded-xl text-sm mb-6"><?= e($err) ?></div>
        <?php endif; ?>

        <form method="POST" action="/reset-password" class="flex flex-col gap-5">
            <?= csrf_field() ?>
            <input type="hidden" name="token" value="<?= e($token) ?>">
            <input type="hidden" name="email" value="<?= e($email) ?>">

            <!-- Email Display -->
            <div class="flex flex-col gap-1.5 opacity-60">
                <label class="text-[0.75rem] font-bold text-text-muted uppercase tracking-wider pl-1">Email</label>
                <input type="text" value="<?= e($email) ?>" disabled class="bg-bg border border-border rounded-xl px-4 py-3 text-sm cursor-not-allowed">
            </div>

            <!-- New Password -->
            <div class="flex flex-col gap-1.5">
                <label class="text-[0.75rem] font-bold text-text-muted uppercase tracking-wider pl-1" for="password">Kata Sandi Baru</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    class="bg-bg border border-border rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all"
                    placeholder="Minimal 6 karakter"
                    required
                    minlength="6"
                    autocomplete="new-password"
                >
            </div>

            <button type="submit" class="bg-primary text-white font-bold py-3 px-6 rounded-xl hover:opacity-90 hover:-translate-y-0.5 transition-all mt-2 cursor-pointer shadow-md">
                Perbarui Kata Sandi
            </button>
        </form>

        <p class="text-text-muted text-[0.7rem] text-center mt-8 uppercase tracking-widest opacity-50">
            &copy; <?= date('Y') ?> <?= e(env('APP_NAME')) ?>
        </p>
    </div>
</div>
