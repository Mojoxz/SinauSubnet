{{-- Form Registrasi Murid --}}
<div>
    <form wire:submit="register">

        {{-- Nama Lengkap --}}
        <div>
            <x-input-label for="name" value="Nama Lengkap" />
            <x-text-input
                wire:model="name"
                id="name"
                class="mt-1 block w-full"
                type="text"
                name="name"
                required
                autofocus
                autocomplete="name"
                placeholder="Nama sesuai rapor"
            />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        {{-- Email --}}
        <div class="mt-4">
            <x-input-label for="email" value="Alamat Email" />
            <x-text-input
                wire:model="email"
                id="email"
                class="mt-1 block w-full"
                type="email"
                name="email"
                required
                autocomplete="username"
                placeholder="nama@sekolah.sch.id"
            />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        {{-- Kelas --}}
        <div class="mt-4">
            <x-input-label for="kelas" value="Kelas" />
            <select
                wire:model="kelas"
                id="kelas"
                name="kelas"
                required
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
            >
                <option value="">— Pilih Kelas —</option>
                @foreach ($pilihanKelas as $k)
                    <option value="{{ $k }}">{{ $k }}</option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('kelas')" class="mt-2" />
        </div>

        {{-- Password --}}
        <div class="mt-4">
            <x-input-label for="password" value="Password" />
            <x-text-input
                wire:model="password"
                id="password"
                class="mt-1 block w-full"
                type="password"
                name="password"
                required
                autocomplete="new-password"
            />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        {{-- Konfirmasi Password --}}
        <div class="mt-4">
            <x-input-label for="password_confirmation" value="Konfirmasi Password" />
            <x-text-input
                wire:model="password_confirmation"
                id="password_confirmation"
                class="mt-1 block w-full"
                type="password"
                name="password_confirmation"
                required
                autocomplete="new-password"
            />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="mt-6 flex items-center justify-between">
            <a href="{{ route('login') }}"
               wire:navigate
               class="text-sm text-gray-600 underline hover:text-gray-900 dark:text-gray-400 dark:hover:text-white">
                Sudah punya akun?
            </a>

            <x-primary-button class="inline-flex items-center gap-2">
                <x-heroicon-o-user-plus class="h-4 w-4" />
                Daftar
            </x-primary-button>
        </div>
    </form>
</div>
