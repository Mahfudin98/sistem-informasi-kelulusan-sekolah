<div class="w-full max-w-[420px] animate-fade-in">
    <div class="bg-surface border border-border rounded-3xl p-10 shadow-lg">
        <div class="text-center mb-10">
            <div class="text-5xl mb-3">🎓</div>
            <h1 class="text-2xl font-black tracking-tight"><?= e(env('APP_NAME')) ?></h1>
            <p class="text-text-muted text-sm mt-1">Login Panel Administrator</p>
        </div>

        <!-- Flash error -->
        <?php if ($err = flash('error')): ?>
            <div class="bg-red-50 border border-red-200 text-red-700 p-4 rounded-xl text-sm mb-6"><?= e($err) ?></div>
        <?php endif; ?>

        <form method="POST" action="/login" id="loginForm" class="flex flex-col gap-5" novalidate>
            <?= csrf_field() ?>

            <!-- Identifier -->
            <div class="flex flex-col gap-1.5">
                <label class="text-[0.75rem] font-bold text-text-muted uppercase tracking-wider pl-1" for="identifier">Username / Email</label>
                <input
                    type="text"
                    id="identifier"
                    name="identifier"
                    class="bg-bg border border-border rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all <?= flash('errors')['identifier'] ?? '' ? 'border-red-500/50 ring-4 ring-red-500/10' : '' ?>"
                    value="<?= e(old('identifier')) ?>"
                    placeholder="Masukkan username atau email"
                    required
                    autocomplete="username"
                >
                <?php if ($err = (flash('errors')['identifier'][0] ?? null)): ?>
                    <span class="text-[0.75rem] text-red-600 pl-1"><?= e($err) ?></span>
                <?php endif; ?>
            </div>

            <!-- Password -->
            <div class="flex flex-col gap-1.5">
                <label class="text-[0.75rem] font-bold text-text-muted uppercase tracking-wider pl-1" for="password">Password</label>
                <div class="relative">
                    <input
                        type="password"
                        id="password"
                        name="password"
                        class="w-full bg-bg border border-border rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all"
                        placeholder="Masukkan password"
                        required
                        autocomplete="current-password"
                    >
                    <button type="button" class="absolute right-3 top-1/2 -translate-y-1/2 text-text-muted hover:text-text transition-colors cursor-pointer" aria-label="Tampilkan password" id="pwToggle">
                        👁
                    </button>
                </div>
                <div class="flex justify-end pr-1">
                    <a href="/forgot-password" class="text-[0.7rem] text-primary font-bold hover:underline">Lupa Password?</a>
                </div>
            </div>

            <button type="submit" class="bg-primary text-white font-bold py-3 px-6 rounded-xl hover:opacity-90 hover:-translate-y-0.5 transition-all mt-4 cursor-pointer shadow-md">
                Masuk ke Dashboard
            </button>
        </form>

        <p class="text-text-muted text-[0.7rem] text-center mt-8 uppercase tracking-widest opacity-50">
            &copy; <?= date('Y') ?> <?= e(env('APP_NAME')) ?>
        </p>
    </div>
</div>
