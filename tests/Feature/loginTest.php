<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\Client;
use Tests\TestCase;

class PassportLoginTest extends TestCase
{
    use RefreshDatabase;

    protected $client;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a Passport password grant client
        $this->client = Client::factory()->create([
            'personal_access_client' => false,
            'password_client' => true,
            'redirect' => '',
        ]);
    }

    public function test_user_can_login_and_receive_token()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('secret123'),
        ]);

        $response = $this->postJson('/oauth/token', [
            'grant_type' => 'password',
            'client_id' => $this->client->id,
            'client_secret' => $this->client->secret,
            'username' => 'test@example.com',
            'password' => 'secret123',
            'scope' => '',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'token_type',
            'expires_in',
            'access_token',
            'refresh_token',
        ]);
    }
}
