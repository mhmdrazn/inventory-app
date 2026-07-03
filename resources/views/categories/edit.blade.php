<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Edit Kategori</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('categories.update', $category) }}">
                        @csrf
                        @method('PUT')
                        <div class="space-y-6">
                            <div>
                                <x-input-label for="name" value="Nama Kategori" />
                                <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $category->name)" required />
                                <x-input-error class="mt-2" :messages="$errors->get('name')" />
                            </div>
                            <div class="flex items-center gap-4">
                                <x-primary-button>Perbarui</x-primary-button>
                                <a href="{{ route('categories.index') }}" class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100">Batal</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
