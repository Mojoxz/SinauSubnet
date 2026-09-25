<?php

declare(strict_types=1);

namespace Tests;

use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * Seeder yang dijalankan otomatis setelah RefreshDatabase.
     * Menjamin role 'guru' dan 'murid' Spatie selalu tersedia di semua test.
     */
    protected string $seeder = RolesAndPermissionsSeeder::class;
}
