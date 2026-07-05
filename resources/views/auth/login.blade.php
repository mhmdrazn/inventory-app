<x-guest-layout>
    <div class="space-y-2 text-center sm:text-left">
        <h1 class="text-2xl font-bold tracking-tight">Masuk ke akun Anda</h1>
        <p class="text-sm text-muted-foreground">Masukkan email dan kata sandi untuk melanjutkan.</p>
    </div>

    @if (session('status'))
        <div class="mt-4">
            <x-ui.alert variant="success">{{ session('status') }}</x-ui.alert>
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="mt-6 space-y-4">
        @csrf

        <div class="space-y-1.5">
            <x-ui.label for="email" value="Email" />
            <x-ui.input id="email" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="nama@warehaus.test" />
            <x-input-error :messages="$errors->get('email')" />
        </div>

        <div class="space-y-1.5">
            <div class="flex items-center justify-between">
                <x-ui.label for="password" value="Kata Sandi" />
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="text-xs text-primary hover:underline">Lupa kata sandi?</a>
                @endif
            </div>
            <x-ui.password-input id="password" name="password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" />
        </div>

        <label class="inline-flex items-center gap-2 text-sm text-muted-foreground">
            <input type="checkbox" name="remember" class="h-4 w-4 rounded border-input text-primary focus:ring-ring focus:ring-offset-0">
            <span>Ingat saya</span>
        </label>

        <x-ui.button type="submit" class="w-full">Masuk</x-ui.button>

        @if (Route::has('register'))
            <p class="text-center text-sm text-muted-foreground">
                Belum memiliki akun?
                <a href="{{ route('register') }}" class="font-medium text-primary hover:underline">Daftar</a>
            </p>
        @endif
    </form>
</x-guest-layout>
