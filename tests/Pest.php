<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
*/

pest()->extend(Tests\TestCase::class)
    ->use(Illuminate\Foundation\Testing\RefreshDatabase::class)
    ->in('Feature', 'Unit');

/*
|--------------------------------------------------------------------------
| Global Expectations
|--------------------------------------------------------------------------
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

expect()->extend('toBePercentage', function () {
    return $this->toBeGreaterThanOrEqual(0)->toBeLessThanOrEqual(100);
});

/*
|--------------------------------------------------------------------------
| Global Helpers
|--------------------------------------------------------------------------
*/

/**
 * Buat user Guru beserta relasi guru dan role Spatie.
 */
function buatGuru(array $attributes = []): \App\Models\User
{
    $user = \App\Models\User::factory()->create($attributes);
    $user->assignRole(\App\Enums\RoleUser::GURU->value);
    \App\Models\Guru::create(['user_id' => $user->id, 'nip' => null]);
    return $user;
}

/**
 * Buat user Murid beserta relasi murid dan role Spatie.
 */
function buatMurid(array $attributes = []): \App\Models\User
{
    $user = \App\Models\User::factory()->create($attributes);
    $user->assignRole(\App\Enums\RoleUser::MURID->value);
    \App\Models\Murid::create([
        'user_id'    => $user->id,
        'kelas'      => $attributes['kelas'] ?? 'XI TKJ 1',
        'total_poin' => 0,
    ]);
    return $user;
}
