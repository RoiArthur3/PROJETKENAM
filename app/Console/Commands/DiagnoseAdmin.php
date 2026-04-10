<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DiagnoseAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'diagnose:admin {email=admin@kenamservices.com} {password=admin123}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Diagnose admin account issues';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        $password = $this->argument('password');

        // Check if user exists
        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->error("User {$email} not found");
            return 1;
        }

        $this->info("User found: ID {$user->id}");

        // Check account status
        $this->line("Account status: ".($user->is_active ? 'Active' : 'INACTIVE'));

        // Check password
        $passwordValid = Hash::check($password, $user->password);
        $this->line("Password valid: ".($passwordValid ? 'YES' : 'NO'));

        // Check roles
        $roles = $user->getRoleNames()->toArray();
        $this->line("Roles: ".(empty($roles) ? 'None' : implode(', ', $roles)));

        // Summary
        if (!$user->is_active) {
            $this->error('Account is inactive - this prevents login');
        }

        if (!$passwordValid) {
            $this->error('Password does not match');
        }

        if (empty($roles)) {
            $this->warn('User has no roles assigned');
        }

        if ($user->is_active && $passwordValid && !empty($roles)) {
            $this->info('Account appears valid and should be able to login');
        }

        return 0;
    }
}
