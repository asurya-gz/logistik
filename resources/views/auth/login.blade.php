<x-layouts.app :title="'Login'">
    <div class="auth-wrap">
        <div class="auth-card card">
            <p class="muted">Masuk ke aplikasi pelatihan</p>
            <h1 class="headline" style="font-size:2.4rem;">Logistik Multi Cabang</h1>

            <form method="POST" action="{{ route('login.store') }}" class="form-grid">
                @csrf
                <div>
                    <label for="email">Email</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus>
                    @error('email') <div class="error-text">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label for="password">Password</label>
                    <input id="password" name="password" type="password" required>
                </div>
                <label style="display:flex;align-items:center;gap:.5rem;">
                    <input type="checkbox" name="remember" value="1" style="width:auto;"> Ingat saya
                </label>
                <button class="button button-primary" type="submit">Login</button>
            </form>

            <div class="help-box" style="margin-top:1rem;">
                <strong>Akun demo</strong>
                <p style="margin:.6rem 0 0;">Super Admin: <code>superadmin@logistik.test</code> / <code>password</code></p>
                <p style="margin:.4rem 0 0;">Admin Cabang: <code>admin.jakarta@logistik.test</code> / <code>password</code></p>
                <p style="margin:.4rem 0 0;">User Cabang: <code>user.jakarta@logistik.test</code> / <code>password</code></p>
            </div>
        </div>
    </div>
</x-layouts.app>
