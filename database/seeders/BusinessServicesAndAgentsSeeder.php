<?php

namespace Database\Seeders;

use App\Models\Service;
use App\Models\ServiceModule;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class BusinessServicesAndAgentsSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            [
                'service' => [
                    'nom' => 'Direction Générale',
                    'code' => 'DG',
                    'email' => 'direction@kenamservices.net',
                    'telephone' => '+221 77 567 89 01',
                    'description' => 'Direction générale de l\'entreprise',
                    'actif' => true,
                ],
                'resource_role' => 'agent',
                'access' => [
                    'can_access_dashboard' => true,
                    'can_access_operations' => true,
                    'can_access_reporting' => true,
                ],
            ],
            [
                'service' => [
                    'nom' => 'Service Achats',
                    'code' => 'ACH',
                    'email' => 'achats@kenamservices.net',
                    'telephone' => '+221 77 456 78 90',
                    'description' => 'Service en charge des achats et des fournisseurs',
                    'actif' => true,
                ],
                'resource_role' => 'agent',
                'access' => [
                    'can_access_dashboard' => true,
                    'can_access_operations' => true,
                    'can_access_suppliers' => true,
                ],
            ],
            [
                'service' => [
                    'nom' => 'Service Logistique',
                    'code' => 'LOG',
                    'email' => 'logistique@kenamservices.net',
                    'telephone' => '+221 77 345 67 89',
                    'description' => 'Service en charge de la gestion des flux et des approvisionnements',
                    'actif' => true,
                ],
                'resource_role' => 'agent',
                'access' => [
                    'can_access_dashboard' => true,
                    'can_access_operations' => true,
                    'can_access_fleet' => true,
                ],
            ],
            [
                'service' => [
                    'nom' => 'Service Qualité',
                    'code' => 'QLT',
                    'email' => 'qualite@kenamservices.net',
                    'telephone' => '+221 77 234 56 78',
                    'description' => 'Service en charge du contrôle qualité et de la conformité',
                    'actif' => true,
                ],
                'resource_role' => 'controleur',
                'access' => [
                    'can_access_dashboard' => true,
                    'can_access_operations' => true,
                    'can_access_audit' => true,
                    'can_access_reporting' => true,
                ],
            ],
            [
                'service' => [
                    'nom' => 'Service Technique',
                    'code' => 'TECH',
                    'email' => 'technique@kenamservices.net',
                    'telephone' => '+221 77 123 45 67',
                    'description' => 'Service en charge de la maintenance et des réparations techniques',
                    'actif' => true,
                ],
                'resource_role' => 'agent',
                'access' => [
                    'can_access_dashboard' => true,
                    'can_access_operations' => true,
                    'can_access_ateliers' => true,
                    'can_access_fleet' => true,
                ],
            ],
            [
                'service' => [
                    'nom' => 'Comptabilité',
                    'code' => 'COMPTA',
                    'email' => 'compta@kenamservices.net',
                    'telephone' => '+221 77 600 00 01',
                    'description' => 'Service comptable et financier',
                    'actif' => true,
                ],
                'resource_role' => 'comptable',
                'access' => [
                    'can_access_dashboard' => true,
                    'can_access_operations' => true,
                    'can_access_accounting' => true,
                    'can_access_invoicing' => true,
                ],
            ],
            [
                'service' => [
                    'nom' => 'Ressources Humaines',
                    'code' => 'RH',
                    'email' => 'rh@kenamservices.net',
                    'telephone' => '+221 77 600 00 02',
                    'description' => 'Gestion du personnel et des ressources humaines',
                    'actif' => true,
                ],
                'resource_role' => 'agent',
                'access' => [
                    'can_access_dashboard' => true,
                    'can_access_operations' => true,
                    'can_access_hr' => true,
                ],
            ],
            [
                'service' => [
                    'nom' => 'Commercial',
                    'code' => 'COM',
                    'email' => 'commercial@kenamservices.net',
                    'telephone' => '+221 77 600 00 03',
                    'description' => 'Ventes, prospection et relation client',
                    'actif' => true,
                ],
                'resource_role' => 'agent',
                'access' => [
                    'can_access_dashboard' => true,
                    'can_access_operations' => true,
                    'can_access_commercial' => true,
                    'can_access_prospection' => true,
                ],
            ],
            [
                'service' => [
                    'nom' => 'Magasin / Stock',
                    'code' => 'STOCK',
                    'email' => 'stock@kenamservices.net',
                    'telephone' => '+221 77 600 00 04',
                    'description' => 'Gestion des stocks et approvisionnements',
                    'actif' => true,
                ],
                'resource_role' => 'magasinier',
                'access' => [
                    'can_access_dashboard' => true,
                    'can_access_operations' => true,
                    'can_access_warehouse' => true,
                    'can_access_suppliers' => true,
                ],
            ],
            [
                'service' => [
                    'nom' => 'Parc Auto',
                    'code' => 'FLEET',
                    'email' => 'parc-auto@kenamservices.net',
                    'telephone' => '+221 77 600 00 05',
                    'description' => 'Gestion de la flotte et des véhicules',
                    'actif' => true,
                ],
                'resource_role' => 'agent',
                'access' => [
                    'can_access_dashboard' => true,
                    'can_access_operations' => true,
                    'can_access_fleet' => true,
                    'can_access_ateliers' => true,
                ],
            ],
            [
                'service' => [
                    'nom' => 'Contrôle & Audit',
                    'code' => 'AUD',
                    'email' => 'audit@kenamservices.net',
                    'telephone' => '+221 77 600 00 07',
                    'description' => 'Contrôle interne, audit et reporting',
                    'actif' => true,
                ],
                'resource_role' => 'controleur',
                'access' => [
                    'can_access_dashboard' => true,
                    'can_access_operations' => true,
                    'can_access_audit' => true,
                    'can_access_reporting' => true,
                ],
            ],
        ];

        foreach ($services as $row) {
            $serviceData = $row['service'];
            $service = Service::updateOrCreate(
                ['email' => $serviceData['email']],
                $serviceData
            );

            $this->syncServiceModulesFromAccessFlags($service, (array) ($row['access'] ?? []));

            $chef = $this->seedChefForService($service);
            if ($chef && \Illuminate\Support\Facades\Schema::hasColumn('services', 'responsable_id')) {
                $service->responsable_id = $chef->id;
                $service->save();
            }

            $this->seedResourcesForService(
                $service,
                (string) ($row['resource_role'] ?? 'agent'),
                (array) ($row['access'] ?? []),
                3
            );
        }
    }

    private function syncServiceModulesFromAccessFlags(Service $service, array $accessFlags): void
    {
        $flagToModule = [
            'can_access_dashboard' => 'dashboard',
            'can_access_operations' => 'operations',
            'can_access_fleet' => 'fleet',
            'can_access_hr' => 'hr',
            'can_access_suppliers' => 'suppliers',
            'can_access_warehouse' => 'warehouse',
            'can_access_accounting' => 'accounting',
            'can_access_invoicing' => 'invoicing',
            'can_access_reporting' => 'reporting',
            'can_access_commercial' => 'commercial',
            'can_access_prospection' => 'prospection',
            'can_access_ateliers' => 'ateliers',
            'can_access_audit' => 'audit',
        ];

        $selectedModules = [];
        foreach ($flagToModule as $flag => $moduleCode) {
            if (!empty($accessFlags[$flag])) {
                $selectedModules[] = $moduleCode;
            }
        }

        // Modules obligatoires par défaut
        $mandatoryModules = ['dashboard', 'operations'];
        $finalModules = array_values(array_unique(array_merge($selectedModules, $mandatoryModules)));

        DB::beginTransaction();
        try {
            ServiceModule::where('service_id', $service->id)->delete();

            foreach ($finalModules as $moduleCode) {
                ServiceModule::create([
                    'service_id' => $service->id,
                    'module_code' => $moduleCode,
                    'can_access' => true,
                ]);
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    private function seedChefForService(Service $service): ?User
    {
        $slug = Str::slug($service->nom, '-');
        $email = "chef.{$slug}@kenamservices.net";
        $username = substr(Str::slug("chef-{$service->code}", ''), 0, 30);

        return User::updateOrCreate(
            ['email' => $email],
            [
                'name' => "Chef de service - {$service->nom}",
                'username' => $username ?: null,
                'phone' => '+221 77 800 00 ' . str_pad((string) (($service->id % 90) + 1), 2, '0', STR_PAD_LEFT),
                'password' => Hash::make('Kenam@2025!'),
                'role' => 'chef_service',
                'service_id' => $service->id,
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );
    }

    private function seedResourcesForService(Service $service, string $role, array $access, int $count): void
    {
        $allowedRoles = ['admin', 'chef_service', 'agent', 'controleur', 'comptable', 'magasinier'];
        if (!in_array($role, $allowedRoles, true)) {
            $role = 'agent';
        }

        for ($i = 1; $i <= $count; $i++) {
            $slug = Str::slug($service->nom, '-');
            $email = "res{$i}.{$slug}@kenamservices.net";
            $username = substr(Str::slug("res{$i}-{$service->code}", ''), 0, 30);

            User::updateOrCreate(
                ['email' => $email],
                array_merge(
                    [
                        'name' => "Ressource {$i} - {$service->nom}",
                        'username' => $username ?: null,
                        'phone' => '+221 77 700 00 ' . str_pad((string) (($service->id % 90) + $i), 2, '0', STR_PAD_LEFT),
                        'password' => Hash::make('Kenam@2025!'),
                        'role' => $role,
                        'service_id' => $service->id,
                        'is_active' => true,
                        'email_verified_at' => now(),
                    ],
                    $access
                )
            );
        }
    }
}
