<?php

namespace Tests\Unit;

use App\Contracts\Subject\SubjectServiceInterface;
use App\Models\Subject;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubjectServiceTest extends TestCase
{
    use RefreshDatabase;

    private SubjectServiceInterface $subjectService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->subjectService = $this->app->make(SubjectServiceInterface::class);
    }

    public function test_it_creates_a_subject(): void
    {
        $data = [
            'name' => 'Mathematics',
            'code' => 'MATH101',
            'description' => 'Basic mathematics.',
        ];

        /** @var Subject $subject */
        $subject = $this->subjectService->create($data);

        $this->assertInstanceOf(Subject::class, $subject);

        $this->assertDatabaseHas('subjects', [
            'code' => 'MATH101',
        ]);
    }

    public function test_it_updates_a_subject(): void
    {
        /** @var Subject $subject */
        $subject = Subject::factory()->create();

        $updated = $this->subjectService->update($subject->id, [
            'name' => 'Updated Subject',
        ]);

        $this->assertEquals('Updated Subject', $updated->name);

        $this->assertDatabaseHas('subjects', [
            'id' => $subject->id,
            'name' => 'Updated Subject',
        ]);
    }

    public function test_it_deletes_a_subject(): void
    {
        $subject = Subject::factory()->create();

        $this->subjectService->delete($subject->id);

        $this->assertSoftDeleted('subjects', [
            'id' => $subject->id,
        ]);
    }
}
