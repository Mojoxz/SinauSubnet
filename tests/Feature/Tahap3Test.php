<?php

declare(strict_types=1);

use App\Models\User;

test('beranda publik bisa diakses', function () {
    $response = $this->get('/');
    $response->assertStatus(200);
    $response->assertSee('Platform Pembelajaran Subnetting');
    $response->assertSee('Daftar');
});

test('halaman tentang bisa diakses', function () {
    $response = $this->get('/tentang');
    $response->assertStatus(200);
    $response->assertSee('Tentang SinauSubnet');
});

test('guest tidak bisa akses dashboard', function () {
    $response = $this->get('/dashboard');
    $response->assertRedirect('/login');
});

test('login sebagai guru diarahkan ke dashboard guru', function () {
    $guru = buatGuru();

    $response = $this->actingAs($guru)->get('/dashboard');
    $response->assertRedirect('/guru/dashboard');

    $this->actingAs($guru)->get('/guru/dashboard')
         ->assertStatus(200)
         ->assertSee('Dashboard Guru');
});

test('login sebagai murid diarahkan ke dashboard murid', function () {
    $murid = buatMurid();

    $response = $this->actingAs($murid)->get('/dashboard');
    $response->assertRedirect('/murid/dashboard');

    $this->actingAs($murid)->get('/murid/dashboard')
         ->assertStatus(200)
         ->assertSee('Dashboard Siswa');
});

test('murid tidak bisa akses area guru', function () {
    $murid = buatMurid();

    $response = $this->actingAs($murid)->get('/guru/dashboard');
    $response->assertStatus(403);
});

test('guru tidak bisa akses area murid', function () {
    $guru = buatGuru();

    $response = $this->actingAs($guru)->get('/murid/dashboard');
    $response->assertStatus(403);
});

test('halaman register murid menampilkan opsi kelas dan memproses registrasi dengan benar', function () {
    $response = $this->get('/register');
    $response->assertStatus(200);
    $response->assertSee('Pilih Kelas');

    // Test proses registrasi class-based via Livewire
    Livewire\Livewire::test(\App\Livewire\Auth\Register::class)
        ->set('name', 'Budi Santoso')
        ->set('email', 'budi@sekolah.sch.id')
        ->set('kelas', 'XI TKJ 1')
        ->set('password', 'password123')
        ->set('password_confirmation', 'password123')
        ->call('register')
        ->assertRedirect('/murid/dashboard');

    // Pastikan tersimpan dengan benar di DB (dengan role Murid)
    $user = User::where('email', 'budi@sekolah.sch.id')->first();
    expect($user)->not->toBeNull();
    expect($user->isMurid())->toBeTrue();
    expect($user->murid->kelas)->toBe('XI TKJ 1');
});
