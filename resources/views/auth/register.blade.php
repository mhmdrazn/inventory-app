<x-guest-layout>
    <div class="space-y-2 text-center sm:text-left">
        <h1 class="text-2xl font-bold tracking-tight">Buat akun baru</h1>
        <p class="text-sm text-muted-foreground">Daftar untuk mengakses sistem inventaris.</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="mt-6 space-y-4">
        @csrf

        <div class="space-y-1.5">
            <x-ui.label for="name" value="Nama Lengkap" />
            <x-ui.input id="name" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" placeholder="Nama lengkap Anda" />
            <x-input-error :messages="$errors->get('name')" />
        </div>

        <div class="space-y-1.5">
            <x-ui.label for="email" value="Email" />
            <x-ui.input id="email" type="email" name="email" :value="old('email')" required autocomplete="username" placeholder="nama@warehaus.test" />
            <x-input-error :messages="$errors->get('email')" />
        </div>

        <div class="space-y-1.5">
            <x-ui.label for="password" value="Kata Sandi" />
            <x-ui.password-input id="password" name="password" required autocomplete="new-password" placeholder="Minimal 8 karakter" />
            <x-input-error :messages="$errors->get('password')" />
        </div>

        <div class="space-y-1.5">
            <x-ui.label for="password_confirmation" value="Konfirmasi Kata Sandi" />
            <x-ui.password-input id="password_confirmation" name="password_confirmation" required autocomplete="new-password" placeholder="Ulangi kata sandi" />
            <x-input-error :messages="$errors->get('password_confirmation')" />
        </div>

        <x-ui.button type="submit" class="w-full">Daftar</x-ui.button>

        <p class="text-center text-sm text-muted-foreground">
            Sudah memiliki akun?
            <a href="{{ route('login') }}" class="font-medium text-primary hover:underline">Masuk</a>
        </p>
    </form>
</x-guest-layout>
