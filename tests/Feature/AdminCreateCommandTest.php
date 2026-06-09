<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Console\Command;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminCreateCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_create_command_creates_admin_user(): void
    {
        $this
            ->artisan('admin:create')
            ->expectsQuestion('Name', 'Production Admin')
            ->expectsQuestion('Email', 'admin@production.test')
            ->expectsQuestion('Password', 'very-secure-password')
            ->expectsQuestion('Confirm password', 'very-secure-password')
            ->expectsOutput('Admin user created or updated.')
            ->assertExitCode(Command::SUCCESS);

        $user = User::query()->where('email', 'admin@production.test')->firstOrFail();

        $this->assertSame('Production Admin', $user->name);
        $this->assertTrue(Hash::check('very-secure-password', $user->password));
    }

    public function test_admin_create_command_rejects_password_mismatch(): void
    {
        $this
            ->artisan('admin:create')
            ->expectsQuestion('Name', 'Production Admin')
            ->expectsQuestion('Email', 'admin@production.test')
            ->expectsQuestion('Password', 'very-secure-password')
            ->expectsQuestion('Confirm password', 'different-password')
            ->assertExitCode(Command::FAILURE);

        $this->assertDatabaseMissing('users', [
            'email' => 'admin@production.test',
        ]);
    }

    public function test_database_seeder_does_not_create_demo_admin_in_production(): void
    {
        $this->app->detectEnvironment(fn () => 'production');

        $this
            ->artisan('db:seed', ['--class' => DatabaseSeeder::class, '--force' => true])
            ->assertExitCode(Command::SUCCESS);

        $this->assertDatabaseMissing('users', [
            'email' => 'admin@example.com',
        ]);
    }
}
