<x-guest-layout>
    <div class="space-y-2 text-center sm:text-left">
        <h1 class="text-2xl font-bold tracking-tight">Atur ulang kata sandi</h1>
        <p class="text-sm text-muted-foreground">Buat kata sandi baru untuk akun Anda.</p>
    </div>

    <form method="POST" action="{{ route('password.store') }}" class="mt-6 space-y-4">
        @csrf
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <div class="space-y-1.5">
            <x-ui.label for="email" value="Email" />
            <x-ui.input id="email" type="email" name="email" :value="old('email', $request->email)" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" />
        </div>

        <div class="space-y-1.5">
            <x-ui.label for="password" value="Kata Sandi Baru" />
            <x-ui.password-input id="password" name="password" required autocomplete="new-password" placeholder="Minimal 8 karakter" />
            <x-input-error :messages="$errors->get('password')" />
        </div>

        <div class="space-y-1.5">
            <x-ui.label for="password_confirmation" value="Konfirmasi Kata Sandi" />
            <x-ui.password-input id="password_confirmation" name="password_confirmation" required autocomplete="new-password" placeholder="Ulangi kata sandi baru" />
            <x-input-error :messages="$errors->get('password_confirmation')" />
        </div>

        <x-ui.button type="submit" class="w-full">Atur Ulang Kata Sandi</x-ui.button>
    </form>
</x-guest-layout>
