<?php

namespace Tests\Feature;

use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class SubjectApiTest extends TestCase
{
    use RefreshDatabase;

    protected function authenticate(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        Sanctum::actingAs($user, ['*']);
    }

    public function test_it_lists_subjects(): void
    {
        $this->authenticate();

        Subject::factory()->count(3)->create();

        $response = $this->getJson('/api/v1/subjects');

        $response
            ->assertOk()
            ->assertJsonStructure([
                'data',
            ]);
    }

    public function test_it_creates_a_subject(): void
    {
        $this->authenticate();

        $payload = [
            'name' => 'Physics',
            'code' => 'PHY101',
            'description' => 'Introductory physics.',
        ];

        $response = $this->postJson('/api/v1/subjects', $payload);

        $response
            ->assertCreated()
            ->assertJsonFragment([
                'code' => 'PHY101',
            ]);

        $this->assertDatabaseHas('subjects', [
            'code' => 'PHY101',
        ]);
    }

    public function test_it_shows_a_subject(): void
    {
        $this->authenticate();

        $subject = Subject::factory()->create();

        $response = $this->getJson('/api/v1/subjects/'.$subject->id);

        $response
            ->assertOk()
            ->assertJsonFragment([
                'id' => $subject->id,
            ]);
    }

    public function test_it_updates_a_subject(): void
    {
        $this->authenticate();

        $subject = Subject::factory()->create();

        $response = $this->putJson('/api/v1/subjects/'.$subject->id, [
            'name' => 'Updated Subject From API',
        ]);

        $response
            ->assertOk()
            ->assertJsonFragment([
                'name' => 'Updated Subject From API',
            ]);

        $this->assertDatabaseHas('subjects', [
            'id' => $subject->id,
            'name' => 'Updated Subject From API',
        ]);
    }

    public function test_it_deletes_a_subject(): void
    {
        $this->authenticate();

        $subject = Subject::factory()->create();

        $response = $this->deleteJson('/api/v1/subjects/'.$subject->id);

        $response->assertNoContent();

        $this->assertDatabaseMissing('subjects', [
            'id' => $subject->id,
        ]);
    }
}
