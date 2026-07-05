<x-guest-layout>
    <div class="space-y-2 text-center sm:text-left">
        <h1 class="text-2xl font-bold tracking-tight">Lupa kata sandi?</h1>
        <p class="text-sm text-muted-foreground">Masukkan email Anda dan kami akan mengirimkan tautan untuk mengatur ulang kata sandi.</p>
    </div>

    @if (session('status'))
        <div class="mt-4">
            <x-ui.alert variant="success">{{ session('status') }}</x-ui.alert>
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}" class="mt-6 space-y-4">
        @csrf

        <div class="space-y-1.5">
            <x-ui.label for="email" value="Email" />
            <x-ui.input id="email" type="email" name="email" :value="old('email')" required autofocus placeholder="nama@warehaus.test" />
            <x-input-error :messages="$errors->get('email')" />
        </div>

        <x-ui.button type="submit" class="w-full">Kirim Tautan Reset</x-ui.button>

        <p class="text-center text-sm text-muted-foreground">
            Kembali ke
            <a href="{{ route('login') }}" class="font-medium text-primary hover:underline">Masuk</a>
        </p>
    </form>
</x-guest-layout>
