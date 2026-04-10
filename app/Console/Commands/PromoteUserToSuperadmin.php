<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class PromoteUserToSuperadmin extends Command
{
    protected $signature = 'user:promote-superadmin {phone} {--force : Do not ask for confirmation}';

    protected $description = 'Promote a user (by phone/telephone/username) to superadmin and enable core module permissions.';

    public function handle(): int
    {
        $phone = (string) $this->argument('phone');

        $user = User::where('telephone', $phone)
            ->orWhere('phone', $phone)
            ->orWhere('username', $phone)
            ->first();

        if (!$user) {
            $this->error('USER_NOT_FOUND');
            return self::FAILURE;
        }

        $this->line('Found user:');
        $this->line(' - id: ' . $user->id);
        $this->line(' - name: ' . $user->name);
        $this->line(' - email: ' . $user->email);
        $this->line(' - role(before): ' . $user->role);

        if (!$this->option('force')) {
            if (!$this->confirm('Promote this user to superadmin and enable permissions?')) {
                $this->warn('Cancelled.');
                return self::SUCCESS;
            }
        }

        $user->role = 'superadmin';
        $user->is_active = 1;

        foreach ([
            'can_access_dashboard',
            'can_access_operations',
            'can_access_comptes',
            'can_access_hr',
            'can_access_fleet',
            'can_access_suppliers',
            'can_access_warehouse',
            'can_access_accounting',
            'can_access_invoicing',
            'can_access_reporting',
            'can_access_commercial',
            'can_access_prospection',
            'can_access_ateliers',
            'can_access_projects',
            'can_access_treasury',
            'can_access_audit',
            'can_access_services',
            'can_access_system',
        ] as $field) {
            if (property_exists($user, $field) || array_key_exists($field, $user->getAttributes())) {
                $user->{$field} = 1;
            }
        }

        $user->save();

        if (method_exists($user, 'roles')) {
            $roleModelClass = '\\App\\Models\\Role';
            if (class_exists($roleModelClass)) {
                $role = $roleModelClass::where('name', 'superadmin')->first();
                if ($role) {
                    try {
                        $user->roles()->syncWithoutDetaching([$role->id]);
                    } catch (\Throwable $e) {
                    }
                }
            }
        }

        $this->info('OK');
        $this->line(' - role(after): ' . $user->role);

        return self::SUCCESS;
    }
}
