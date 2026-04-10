<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Service;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UnifiedRegistrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Créer un service actif pour les tests
        Service::create([
            'nom' => 'Service Test',
            'code' => 'TEST',
            'actif' => true,
        ]);
        
        // Créer les tables nécessaires si elles manquent dans sqlite memory
        // ou utiliser les migrations normales via RefreshDatabase
    }

    public function test_registration_screen_can_be_rendered()
    {
        $response = $this->get('/register');
        $response->assertStatus(200);
        $response->assertSee('Téléphone'); // Nouveau champ
        $response->assertSee('Service rattaché'); // Nouveau champ
        
        // Vérifier que le service créé est visible
        $response->assertSee('Service Test');
    }

    public function test_new_users_can_register_as_agent()
    {
        $service = Service::first();

        $response = $this->post('/register', [
            'name' => 'Test Agent',
            'email' => 'agent@example.com',
            'phone' => '+225 01 02 03 04',
            'password' => 'password',
            'password_confirmation' => 'password',
            'service_id' => $service->id,
            'role' => 'agent',
            // Pas de modules envoyés pour l'agent (auto-sélection)
        ]);

        $this->assertAuthenticated();
        
        // Vérifier les données en base
        $user = User::where('email', 'agent@example.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals('agent', $user->role);
        $this->assertEquals('+225 01 02 03 04', $user->phone);
        $this->assertTrue((bool)$user->is_active);
        
        // Vérifier l'auto-sélection des modules
        $this->assertTrue((bool)$user->can_access_operations);
        $this->assertTrue((bool)$user->can_access_requetes);
        
        $response->assertRedirect(route('dashboard', absolute: false));
    }

    public function test_new_users_can_register_as_moderator()
    {
        $service = Service::first();

        $response = $this->post('/register', [
            'name' => 'Test Modérateur',
            'email' => 'mod@example.com',
            'phone' => '0708091011',
            'password' => 'password',
            'password_confirmation' => 'password',
            'service_id' => $service->id,
            'role' => 'moderator',
            'modules' => ['rh', 'stock'] // Modules choisis
        ]);

        $this->assertAuthenticated();

        $user = User::where('email', 'mod@example.com')->first();
        $this->assertEquals('moderator', $user->role);
        
        // Vérifier les modules sélectionnés
        $this->assertTrue((bool)$user->can_access_rh);
        $this->assertTrue((bool)$user->can_access_stock);
        // Vérifier qu'un module NON sélectionné est false
        $this->assertFalse((bool)$user->can_access_comptabilite);
        
        $response->assertRedirect(route('dashboard', absolute: false));
    }

    public function test_admin_can_create_user_with_unified_form()
    {
        // Créer un admin
        $admin = User::factory()->create([
            'role' => 'admin', 
            'can_access_system' => true
        ]);
        
        $service = Service::first();

        $response = $this->actingAs($admin)->post(route('admin.users.store'), [
            'name' => 'Created by Admin',
            'email' => 'created@admin.com',
            'phone' => '0505050505',
            'password' => 'password',
            'password_confirmation' => 'password',
            'service_id' => $service->id,
            'role' => 'agent',
        ]);

        $response->assertRedirect(route('admin.users.index'));
        
        $user = User::where('email', 'created@admin.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals('agent', $user->role);
        $this->assertTrue((bool)$user->can_access_operations);
    }

    public function test_users_can_login_with_phone()
    {
        $user = User::factory()->create([
            'phone' => '0707070707',
            'password' => bcrypt('password123'),
            'role' => 'agent',
            'is_active' => true,
        ]);

        $response = $this->post('/login', [
            'phone' => '0707070707',
            'password' => 'password123',
        ]);

        $this->assertAuthenticatedAs($user);
        $response->assertRedirect('/agent/requetes');
    }
}
