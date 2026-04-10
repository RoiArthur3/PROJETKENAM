<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class CreateAdminUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:admin
        {--email=admin@kenamservices.com : Admin email}
        {--password=admin123 : Admin password}
        {--name=Admin : Admin name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new admin user with superadmin role';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->option('email');
        $password = $this->option('password');
        $name = $this->option('name');

        // Delete existing admin user if exists
        $existingUser = User::where('email', $email)->first();
        if ($existingUser) {
            $existingUser->delete();
            $this->info('Existing admin user deleted.');
        }

        // Generate unique username
        $baseUsername = strtok($email, '@');
        $username = $baseUsername;
        $counter = 1;

        while (User::where('username', $username)->exists()) {
            $username = $baseUsername . $counter;
            $counter++;
        }

        // Create user
        $user = User::create([
            'name' => $name,
            'email' => $email,
            'username' => $username,
            'password' => Hash::make($password),
            'role' => 'admin',
            'is_active' => true,
        ]);

        // Ensure superadmin role exists
        Role::firstOrCreate(['name' => 'superadmin', 'guard_name' => 'web']);

        // Assign superadmin role
        $user->assignRole('superadmin');

        $this->info('Admin user created successfully!');
        $this->line("Email: {$email}");
        $this->line("Username: {$username}");
        $this->line("Password: {$password}");

        return 0;
    }
}
