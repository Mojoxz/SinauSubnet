<?php

declare(strict_types=1);

namespace App\Livewire\Auth;

use App\Enums\RoleUser;
use App\Models\Murid;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.guest')]
class Register extends Component
{
    public string $name   = '';
    public string $email  = '';
    public string $kelas  = '';
    public string $password = '';
    public string $password_confirmation = '';

    /** Pilihan kelas yang tersedia — disesuaikan dengan data sekolah. */
    public array $pilihanKelas = [
        'X TKJ 1', 'X TKJ 2',
        'XI TKJ 1', 'XI TKJ 2',
        'XII TKJ 1', 'XII TKJ 2',
    ];

    public function register(): void
    {
        $validated = $this->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'kelas'    => ['required', 'string', 'in:' . implode(',', $this->pilihanKelas)],
            'password' => ['required', 'string', 'confirmed', Password::defaults()],
        ], [
            'kelas.required' => 'Silakan pilih kelas Anda.',
            'kelas.in'       => 'Kelas yang dipilih tidak valid.',
        ]);

        DB::transaction(function () use ($validated) {
            $user = User::create([
                'name'     => $validated['name'],
                'email'    => $validated['email'],
                'password' => Hash::make($validated['password']),
            ]);

            $user->assignRole(RoleUser::MURID->value);

            Murid::create([
                'user_id' => $user->id,
                'kelas'   => $validated['kelas'],
            ]);

            event(new Registered($user));
            Auth::login($user);
        });

        $this->redirect(route('murid.dashboard', absolute: false), navigate: true);
    }

    public function render()
    {
        return view('livewire.auth.register');
    }
}
