<div class="auth-container">
    <div class="auth-card">
        <div class="auth-header">
            <div class="auth-logo">🎓</div>
            <h1 class="auth-title"><?= e(env('APP_NAME')) ?></h1>
            <p class="auth-subtitle">Login Panel Administrator</p>
        </div>

        <!-- Flash error -->
        <?php if ($err = flash('error')): ?>
            <div class="alert alert-danger"><?= e($err) ?></div>
        <?php endif; ?>

        <form method="POST" action="/login" id="loginForm" novalidate>
            <?= csrf_field() ?>

            <!-- Identifier -->
            <div class="form-group">
                <label class="form-label" for="identifier">Username / Email</label>
                <input
                    type="text"
                    id="identifier"
                    name="identifier"
                    class="form-input <?= flash('errors')['identifier'] ?? '' ? 'is-invalid' : '' ?>"
                    value="<?= e(old('identifier')) ?>"
                    placeholder="Masukkan username atau email"
                    required
                    autocomplete="username"
                >
                <?php if ($err = (flash('errors')['identifier'][0] ?? null)): ?>
                    <span class="form-error"><?= e($err) ?></span>
                <?php endif; ?>
            </div>

            <!-- Password -->
            <div class="form-group">
                <label class="form-label" for="password">Password</label>
                <div class="input-password-wrapper">
                    <input
                        type="password"
                        id="password"
                        name="password"
                        class="form-input"
                        placeholder="Masukkan password"
                        required
                        autocomplete="current-password"
                    >
                    <button type="button" class="password-toggle" aria-label="Tampilkan password" id="pwToggle">
                        👁
                    </button>
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-full mt-4">
                Masuk ke Dashboard
            </button>
        </form>

        <p class="auth-footer">
            &copy; <?= date('Y') ?> <?= e(env('APP_NAME')) ?>
        </p>
    </div>
</div>
