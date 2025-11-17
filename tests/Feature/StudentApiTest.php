<?php

namespace Tests\Feature;

use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class StudentApiTest extends TestCase
{
    use RefreshDatabase;

    protected function authenticate(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        Sanctum::actingAs($user, ['*']);
    }

    public function test_it_lists_students(): void
    {
        $this->authenticate();

        Student::factory()->count(3)->create();

        $response = $this->getJson('/api/v1/students');

        $response
            ->assertOk()
            ->assertJsonStructure([
                'data',
            ]);
    }

    public function test_it_creates_a_student(): void
    {
        $this->authenticate();

        $payload = [
            'name' => 'API Student',
            'email' => 'api-student@example.com',
            'enrollment_number' => 'ENR99999',
            'birth_date' => '2001-01-01',
        ];

        $response = $this->postJson('/api/v1/students', $payload);

        $response
            ->assertCreated()
            ->assertJsonFragment([
                'email' => 'api-student@example.com',
            ]);

        $this->assertDatabaseHas('students', [
            'email' => 'api-student@example.com',
        ]);
    }

    public function test_it_shows_a_student(): void
    {
        $this->authenticate();

        $student = Student::factory()->create();

        $response = $this->getJson('/api/v1/students/'.$student->id);

        $response
            ->assertOk()
            ->assertJsonFragment([
                'id' => $student->id,
            ]);
    }

    public function test_it_updates_a_student(): void
    {
        $this->authenticate();

        /** @var Student $student */
        $student = Student::factory()->create();

        $response = $this->putJson('/api/v1/students/'.$student->id, [
            'name' => 'Updated From API',
            'email' => 'updated-student@example.com',
            'enrollment_number' => 'ENR12345',
            'birth_date' => '2002-02-02',
        ]);

        $response
            ->assertOk()
            ->assertJsonFragment([
                'name' => 'Updated From API',
            ]);

        $this->assertDatabaseHas('students', [
            'id' => $student->id,
            'name' => 'Updated From API',
        ]);
    }

    public function test_it_deletes_a_student(): void
    {
        $this->authenticate();

        $student = Student::factory()->create();

        $response = $this->deleteJson('/api/v1/students/'.$student->id);

        $response->assertNoContent();

        $this->assertSoftDeleted('students', [
            'id' => $student->id,
        ]);
    }
}
