<?php

namespace Tests\Unit;

use App\Contracts\Student\StudentServiceInterface;
use App\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentServiceTest extends TestCase
{
    use RefreshDatabase;

    private StudentServiceInterface $studentService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->studentService = $this->app->make(StudentServiceInterface::class);
    }

    public function test_it_creates_a_student(): void
    {
        $data = [
            'name' => 'Test Student',
            'email' => 'student@example.com',
            'enrollment_number' => 'ENR00001',
            'birth_date' => '2000-01-01',
        ];

        /** @var Student $student */
        $student = $this->studentService->create($data);

        $this->assertInstanceOf(Student::class, $student);

        $this->assertDatabaseHas('students', [
            'email' => 'student@example.com',
        ]);
    }

    public function test_it_updates_a_student(): void
    {
        /** @var Student $student */
        $student = Student::factory()->create();

        $updated = $this->studentService->update($student->id, [
            'name' => 'Updated Name',
        ]);

        $this->assertEquals('Updated Name', $updated->name);

        $this->assertDatabaseHas('students', [
            'id' => $student->id,
            'name' => 'Updated Name',
        ]);
    }

    public function test_it_deletes_a_student(): void
    {
        $student = Student::factory()->create();

        $this->studentService->delete($student->id);

        $this->assertSoftDeleted('students', [
            'id' => $student->id,
        ]);
    }
}
