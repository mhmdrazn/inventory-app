<x-guest-layout>
    <div class="space-y-2 text-center sm:text-left">
        <h1 class="text-2xl font-bold tracking-tight">Verifikasi email Anda</h1>
        <p class="text-sm text-muted-foreground">Terima kasih telah mendaftar! Silakan periksa email Anda untuk tautan verifikasi. Jika belum menerimanya, kami akan dengan senang hati mengirimkan yang baru.</p>
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mt-4">
            <x-ui.alert variant="success">Tautan verifikasi baru telah dikirim ke email Anda.</x-ui.alert>
        </div>
    @endif

    <div class="mt-6 flex items-center gap-3">
        <form method="POST" action="{{ route('verification.send') }}" class="flex-1">
            @csrf
            <x-ui.button type="submit" class="w-full">Kirim Ulang Email Verifikasi</x-ui.button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <x-ui.button type="submit" variant="ghost">Keluar</x-ui.button>
        </form>
    </div>
</x-guest-layout>
