<x-guest-layout>
    <div class="space-y-2 text-center sm:text-left">
        <h1 class="text-2xl font-bold tracking-tight">Konfirmasi kata sandi</h1>
        <p class="text-sm text-muted-foreground">Ini adalah area aman. Mohon konfirmasi kata sandi Anda untuk melanjutkan.</p>
    </div>

    <form method="POST" action="{{ route('password.confirm') }}" class="mt-6 space-y-4">
        @csrf

        <div class="space-y-1.5">
            <x-ui.label for="password" value="Kata Sandi" />
            <x-ui.input id="password" type="password" name="password" required autocomplete="current-password" placeholder="••••••••" autofocus />
            <x-input-error :messages="$errors->get('password')" />
        </div>

        <x-ui.button type="submit" class="w-full">Konfirmasi</x-ui.button>
    </form>
</x-guest-layout>
