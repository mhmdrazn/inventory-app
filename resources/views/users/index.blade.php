<x-app-layout>
    <x-slot name="header">Pengguna</x-slot>

    <div class="mx-auto max-w-7xl space-y-6">
        {{-- Page header --}}
        <div class="flex flex-col items-start justify-between gap-3 sm:flex-row sm:items-center">
            <div>
                <h1 class="text-2xl font-bold tracking-tight">Manajemen Pengguna</h1>
                <p class="text-sm text-muted-foreground">Kelola akun pengguna dan peran akses.</p>
            </div>
            <x-ui.button @click="$dispatch('open-dialog', 'create-user')">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                Tambah Pengguna
            </x-ui.button>
        </div>

        {{-- Flash --}}
        @if(session('success'))
            <x-ui.alert variant="success">{{ session('success') }}</x-ui.alert>
        @endif
        @if(session('error'))
            <x-ui.alert variant="destructive">{{ session('error') }}</x-ui.alert>
        @endif

        {{-- Stat cards by role --}}
        @php
            $roleCounts = $users->getCollection()->groupBy(fn($u) => $u->role->name);
            $roleAllCounts = \App\Models\User::selectRaw('role_id, count(*) as total')->groupBy('role_id')->pluck('total', 'role_id');
        @endphp
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
            @foreach($roles as $role)
                @php
                    $count = $roleAllCounts[$role->id] ?? 0;
                    $color = match($role->name) {
                        'admin' => ['bg-primary/10 text-primary', 'ring-primary/20'],
                        'staff' => ['bg-blue-500/10 text-blue-600 dark:text-blue-400', 'ring-blue-500/20'],
                        'manager' => ['bg-emerald-500/10 text-emerald-600 dark:text-emerald-400', 'ring-emerald-500/20'],
                        default => ['bg-muted text-muted-foreground', 'ring-border'],
                    };
                @endphp
                <x-ui.card>
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-muted-foreground">Total {{ ucfirst($role->name) }}</p>
                            <p class="mt-1 text-2xl font-bold tracking-tight">{{ $count }}</p>
                        </div>
                        <div class="flex h-10 w-10 items-center justify-center rounded-lg {{ $color[0] }} ring-1 ring-inset {{ $color[1] }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0zM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632z" /></svg>
                        </div>
                    </div>
                </x-ui.card>
            @endforeach
        </div>

        {{-- Filter --}}
        <x-ui.card>
            <form method="GET" action="{{ route('users.index') }}" class="grid grid-cols-1 gap-3 sm:grid-cols-3">
                <div class="space-y-1.5">
                    <x-ui.label for="search" value="Cari" />
                    <x-ui.input id="search" name="search" placeholder="Nama atau email..." :value="request('search')" />
                </div>
                <div class="space-y-1.5">
                    <x-ui.label for="role" value="Role" />
                    <x-ui.select id="role" name="role">
                        <option value="">Semua Role</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}" {{ request('role') == $role->id ? 'selected' : '' }}>{{ ucfirst($role->name) }}</option>
                        @endforeach
                    </x-ui.select>
                </div>
                <div class="flex items-end gap-2">
                    <x-ui.button type="submit" class="flex-1 w-full">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" /></svg>
                        Cari
                    </x-ui.button>
                    <x-ui.button variant="outline" :href="route('users.index')" class="flex-1 w-full">Reset</x-ui.button>
                </div>
            </form>
        </x-ui.card>

        {{-- Table --}}
        <x-ui.card :padded="false">
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="border-b bg-muted/40 text-xs text-muted-foreground">
                            <th class="px-6 py-3 text-left font-medium">Pengguna</th>
                            <th class="px-6 py-3 text-left font-medium">Email</th>
                            <th class="px-6 py-3 text-left font-medium">Role</th>
                            <th class="px-6 py-3 text-left font-medium">Bergabung</th>
                            <th class="px-6 py-3 text-right font-medium">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm">
                        @forelse($users as $u)
                            <tr class="border-b last:border-0 hover:bg-muted/30 transition-colors">
                                <td class="px-6 py-3">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-primary/10 text-primary text-xs font-semibold">
                                            {{ strtoupper(substr($u->name, 0, 1)) }}
                                        </div>
                                        <div class="min-w-0">
                                            <p class="font-medium truncate">{{ $u->name }}</p>
                                            @if($u->id === auth()->id())
                                                <p class="text-xs text-muted-foreground">Anda</p>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-3 text-muted-foreground">{{ $u->email }}</td>
                                <td class="px-6 py-3">
                                    @if($u->role->name === 'admin')
                                        <x-ui.badge variant="default">Admin</x-ui.badge>
                                    @elseif($u->role->name === 'staff')
                                        <x-ui.badge variant="info">Staff</x-ui.badge>
                                    @else
                                        <x-ui.badge variant="success">Manager</x-ui.badge>
                                    @endif
                                </td>
                                <td class="px-6 py-3 text-muted-foreground">{{ $u->created_at->format('d M Y') }}</td>
                                <td class="px-6 py-3">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <x-ui.button
                                            variant="outline"
                                            size="sm"
                                            @click="$dispatch('open-dialog', 'edit-user-{{ $u->id }}')"
                                        >
                                            Edit
                                        </x-ui.button>
                                        @if($u->id !== auth()->id())
                                            <div x-data="{ showDeleteModal: false }" class="inline-flex">
                                                <x-ui.button variant="soft-destructive" size="sm" @click="showDeleteModal = true">Hapus</x-ui.button>
                                                <div x-show="showDeleteModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
                                                    <div @click="showDeleteModal = false" class="fixed inset-0 bg-black/50 backdrop-blur-sm"></div>
                                                    <div class="relative flex min-h-screen items-center justify-center p-4">
                                                        <div @click.stop class="relative w-full max-w-md rounded-lg border bg-card p-6 shadow-lg">
                                                            <div class="flex items-start gap-3">
                                                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-destructive/10 text-destructive">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0zm-9 3.75h.008v.008H12v-.008z" /></svg>
                                                                </div>
                                                                <div class="text-left">
                                                                    <h3 class="text-lg font-semibold">Konfirmasi Hapus</h3>
                                                                    <p class="mt-1 text-sm text-muted-foreground">Yakin ingin menghapus pengguna <strong class="text-foreground">{{ $u->name }}</strong>? Tindakan ini tidak dapat dibatalkan.</p>
                                                                </div>
                                                            </div>
                                                            <div class="mt-6 flex justify-end gap-2">
                                                                <x-ui.button variant="outline" @click="showDeleteModal = false">Batal</x-ui.button>
                                                                <form method="POST" action="{{ route('users.destroy', $u) }}">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <x-ui.button variant="destructive" type="submit">Ya, Hapus</x-ui.button>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </td>
                            </tr>

                            {{-- Per-row Edit dialog --}}
                            <x-ui.dialog
                                name="edit-user-{{ $u->id }}"
                                title="Edit Pengguna"
                                description="Perbarui data pengguna."
                                maxWidth="lg"
                            >
                                <form method="POST" action="{{ route('users.update', $u) }}" class="space-y-4">
                                    @csrf
                                    @method('PUT')
                                    <div class="space-y-1.5">
                                        <x-ui.label for="edit-name-{{ $u->id }}" value="Nama" />
                                        <x-ui.input id="edit-name-{{ $u->id }}" name="name" :value="old('name', $u->name)" required />
                                    </div>
                                    <div class="space-y-1.5">
                                        <x-ui.label for="edit-email-{{ $u->id }}" value="Email" />
                                        <x-ui.input id="edit-email-{{ $u->id }}" name="email" type="email" :value="old('email', $u->email)" required />
                                    </div>
                                    <div class="space-y-1.5">
                                        <x-ui.label for="edit-role-{{ $u->id }}" value="Role" />
                                        <x-ui.select id="edit-role-{{ $u->id }}" name="role_id" required>
                                            @foreach($roles as $role)
                                                <option value="{{ $role->id }}" {{ old('role_id', $u->role_id) == $role->id ? 'selected' : '' }}>{{ ucfirst($role->name) }}</option>
                                            @endforeach
                                        </x-ui.select>
                                    </div>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        <div class="space-y-1.5">
                                            <x-ui.label for="edit-password-{{ $u->id }}" value="Kata Sandi Baru" />
                                            <x-ui.password-input id="edit-password-{{ $u->id }}" name="password" placeholder="Kosongkan jika tidak berubah" />
                                        </div>
                                        <div class="space-y-1.5">
                                            <x-ui.label for="edit-password-conf-{{ $u->id }}" value="Konfirmasi Kata Sandi" />
                                            <x-ui.password-input id="edit-password-conf-{{ $u->id }}" name="password_confirmation" placeholder="Ulangi kata sandi baru" />
                                        </div>
                                    </div>
                                    <p class="text-xs text-muted-foreground">Kosongkan kolom kata sandi jika tidak ingin mengubahnya.</p>
                                    <div class="flex items-center justify-end gap-2 border-t pt-4">
                                        <x-ui.button type="button" variant="outline" @click="$dispatch('close-dialog', 'edit-user-{{ $u->id }}')">Batal</x-ui.button>
                                        <x-ui.button type="submit">Simpan Perubahan</x-ui.button>
                                    </div>
                                </form>
                            </x-ui.dialog>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-sm text-muted-foreground">Belum ada pengguna.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($users->hasPages())
                <div class="border-t p-4">
                    {{ $users->links() }}
                </div>
            @endif
        </x-ui.card>
    </div>

    {{-- Create User Dialog --}}
    <x-ui.dialog
        name="create-user"
        title="Tambah Pengguna"
        description="Buat akun pengguna baru dan tetapkan peran akses."
        maxWidth="lg"
    >
        <form method="POST" action="{{ route('users.store') }}" class="space-y-4">
            @csrf
            <div class="space-y-1.5">
                <x-ui.label for="new-name" value="Nama Lengkap" />
                <x-ui.input id="new-name" name="name" :value="old('name')" required autofocus />
                <x-input-error :messages="$errors->get('name')" />
            </div>
            <div class="space-y-1.5">
                <x-ui.label for="new-email" value="Email" />
                <x-ui.input id="new-email" name="email" type="email" :value="old('email')" required placeholder="nama@warehaus.test" />
                <x-input-error :messages="$errors->get('email')" />
            </div>
            <div class="space-y-1.5">
                <x-ui.label for="new-role" value="Role" />
                <x-ui.select id="new-role" name="role_id" required>
                    <option value="">Pilih Role</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>{{ ucfirst($role->name) }}</option>
                    @endforeach
                </x-ui.select>
                <x-input-error :messages="$errors->get('role_id')" />
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="space-y-1.5">
                    <x-ui.label for="new-password" value="Kata Sandi" />
                    <x-ui.password-input id="new-password" name="password" required placeholder="Minimal 8 karakter" />
                    <x-input-error :messages="$errors->get('password')" />
                </div>
                <div class="space-y-1.5">
                    <x-ui.label for="new-password-conf" value="Konfirmasi Kata Sandi" />
                    <x-ui.password-input id="new-password-conf" name="password_confirmation" required placeholder="Ulangi kata sandi" />
                </div>
            </div>
            <div class="flex items-center justify-end gap-2 border-t pt-4">
                <x-ui.button type="button" variant="outline" @click="$dispatch('close-dialog', 'create-user')">Batal</x-ui.button>
                <x-ui.button type="submit">Simpan Pengguna</x-ui.button>
            </div>
        </form>
    </x-ui.dialog>

    @if($errors->any() || request('create'))
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                window.dispatchEvent(new CustomEvent('open-dialog', { detail: 'create-user' }));
            });
        </script>
    @endif

    @if(request('edit'))
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                window.dispatchEvent(new CustomEvent('open-dialog', { detail: 'edit-user-{{ (int) request('edit') }}' }));
            });
        </script>
    @endif
</x-app-layout>
