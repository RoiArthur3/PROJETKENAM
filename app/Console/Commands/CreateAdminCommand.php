<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class CreateAdminCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:admin';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create an admin user with full access';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $user = User::create([
            'name' => 'Admin Kenam',
            'email' => 'admin@kenamservices.net',
            'username' => 'admin',
            'password' => Hash::make('admin123'),
            'role' => 'superadmin',
            'is_active' => 1,
            'can_access_dashboard' => 1,
            'can_access_operations' => 1,
            'can_access_hr' => 1,
            'can_access_fleet' => 1,
            'can_access_suppliers' => 1,
            'can_access_warehouse' => 1,
            'can_access_accounting' => 1,
            'can_access_invoicing' => 1,
            'can_access_reporting' => 1,
            'can_access_commercial' => 1,
            'can_access_prospection' => 1,
            'can_access_ateliers' => 1,
            'can_access_projects' => 1,
            'can_access_treasury' => 1,
            'can_access_audit' => 1,
            'can_access_services' => 1,
            'can_access_comptes' => 1,
            'can_access_system' => 1,
            'modules' => json_encode(['dashboard', 'operations', 'hr', 'fleet', 'suppliers', 'warehouse', 'accounting', 'invoicing', 'reporting', 'commercial', 'prospection', 'ateliers', 'projects', 'treasury', 'audit', 'services', 'comptes', 'system']),
            'submodules' => json_encode(['*']),
            'email_verified_at' => now(),
        ]);

        $this->info('Admin user created successfully!');
        $this->info('Email: admin@kenamservices.net');
        $this->info('Password: admin123');
        $this->info('User ID: ' . $user->id);

        return 0;
    }
}
