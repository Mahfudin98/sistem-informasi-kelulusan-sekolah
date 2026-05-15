<div class="w-full max-w-[420px] animate-fade-in">
    <div class="bg-surface border border-border rounded-3xl p-10 shadow-lg">
        <div class="text-center mb-10">
            <div class="text-5xl mb-3">🔑</div>
            <h1 class="text-2xl font-black tracking-tight">Lupa Kata Sandi</h1>
            <p class="text-text-muted text-sm mt-1">Masukkan email Anda untuk menerima link reset</p>
        </div>

        <!-- Flash messages -->
        <?php if ($err = flash('error')): ?>
            <div class="bg-red-50 border border-red-200 text-red-700 p-4 rounded-xl text-sm mb-6"><?= e($err) ?></div>
        <?php endif; ?>
        
        <?php if ($success = flash('success')): ?>
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 p-4 rounded-xl text-sm mb-6"><?= e($success) ?></div>
        <?php endif; ?>

        <form method="POST" action="/forgot-password" class="flex flex-col gap-5">
            <?= csrf_field() ?>

            <!-- Email -->
            <div class="flex flex-col gap-1.5">
                <label class="text-[0.75rem] font-bold text-text-muted uppercase tracking-wider pl-1" for="email">Alamat Email</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    class="bg-bg border border-border rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all"
                    placeholder="nama@sekolah.sch.id"
                    required
                    autocomplete="email"
                >
            </div>

            <button type="submit" class="bg-primary text-white font-bold py-3 px-6 rounded-xl hover:opacity-90 hover:-translate-y-0.5 transition-all mt-2 cursor-pointer shadow-md">
                Kirim Link Reset
            </button>
        </form>

        <div class="mt-8 text-center">
            <a href="/login" class="text-primary text-sm font-bold hover:underline"> Kembali ke Halaman Login </a>
        </div>

        <p class="text-text-muted text-[0.7rem] text-center mt-8 uppercase tracking-widest opacity-50">
            &copy; <?= date('Y') ?> <?= e(env('APP_NAME')) ?>
        </p>
    </div>
</div>
