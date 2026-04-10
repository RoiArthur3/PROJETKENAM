<?php
// tests/Feature/ActionButtonsTest.php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ActionButtonsTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * Test that all main action buttons are present and clickable on key pages.
     * This is a smoke test: it checks for HTTP 200 and presence of button labels.
     */
    public function test_action_buttons_are_visible_on_main_pages()
    {
        $pages = [
            '/',
            '/operations',
            '/operations/create',
            '/validations',
            '/vehicules',
            '/fournisseurs/list',
            '/comptabilite/factures',
            '/tresorerie/avances',
            // Ajoute ici d'autres routes principales à tester
        ];

        foreach ($pages as $url) {
            $response = $this->actingAs($this->getFirstUser())->get($url);
            $response->assertStatus(200);
            // Vérifie la présence d'au moins un bouton d'action
            $response->assertSee('btn'); // Cherche la classe Bootstrap ou Tailwind
        }
    }

    /**
     * Helper pour obtenir un utilisateur authentifié.
     */
    protected function getFirstUser()
    {
        return \App\Models\User::first() ?? \App\Models\User::factory()->create();
    }
}
