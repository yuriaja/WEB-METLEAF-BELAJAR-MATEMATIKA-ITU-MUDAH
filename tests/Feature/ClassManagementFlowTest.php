<?php

namespace Tests\Feature;

use App\Models\Activity;
use App\Models\Assessment;
use App\Models\AssessmentAttempt;
use App\Models\ForumComment;
use App\Models\ForumThread;
use App\Models\Material;
use App\Models\SchoolClass;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ClassManagementFlowTest extends TestCase
{
    use RefreshDatabase;

    private function account(string $role, string $suffix): User
    {
        return User::create([
            'name' => ucfirst($role).' '.$suffix,
            'email' => strtolower($role.$suffix).'@example.test',
            'password' => 'password123',
            'role' => $role,
        ]);
    }

    private function schoolClass(User $teacher, string $code): SchoolClass
    {
        return SchoolClass::create([
            'teacher_id' => $teacher->id,
            'name' => 'VII '.$code,
            'join_code' => $code,
        ]);
    }

    public function test_teacher_can_remove_member_without_deleting_student_account_or_other_class_membership(): void
    {
        $teacher = $this->account('guru', 'remove');
        $student = $this->account('siswa', 'remove');
        $class = $this->schoolClass($teacher, 'KICK111');
        $otherClass = $this->schoolClass($teacher, 'KICK222');
        $class->students()->attach($student->id);
        $otherClass->students()->attach($student->id);

        $quiz = Assessment::create([
            'class_id' => $class->id, 'author_id' => $teacher->id,
            'title' => 'Nilai Terdahulu', 'type' => 'kuis', 'is_published' => true,
        ]);
        $attempt = AssessmentAttempt::create([
            'assessment_id' => $quiz->id, 'user_id' => $student->id,
            'attempt_number' => 1, 'score' => 85, 'status' => 'submitted',
        ]);

        $this->actingAs($teacher)->get(route('classroom.home'))
            ->assertOk()
            ->assertSee(route('classroom.students.remove', [$class->id, $student->id]));

        $this->actingAs($teacher)
            ->delete(route('classroom.students.remove', [$class->id, $student->id]))
            ->assertRedirect(route('classroom.home'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('class_user', ['class_id' => $class->id, 'user_id' => $student->id]);
        $this->assertDatabaseHas('class_user', ['class_id' => $otherClass->id, 'user_id' => $student->id]);
        $this->assertDatabaseHas('users', ['id' => $student->id]);
        $this->assertDatabaseHas('assessment_attempts', ['id' => $attempt->id, 'score' => 85]);
    }

    public function test_only_class_owner_can_remove_member_or_delete_class(): void
    {
        $teacher = $this->account('guru', 'owner');
        $otherTeacher = $this->account('guru', 'outsider');
        $student = $this->account('siswa', 'owner');
        $class = $this->schoolClass($teacher, 'OWNER1');
        $class->students()->attach($student->id);
        $removeUrl = route('classroom.students.remove', [$class->id, $student->id]);
        $deleteUrl = route('classroom.destroy', $class->id);

        $this->delete($removeUrl)->assertRedirect(route('login'));
        $this->actingAs($otherTeacher)->delete($removeUrl)->assertForbidden();
        $this->actingAs($student)->delete($removeUrl)->assertForbidden();
        $this->actingAs($otherTeacher)->delete($deleteUrl, ['confirmation' => $class->name])->assertForbidden();
        $this->actingAs($student)->delete($deleteUrl, ['confirmation' => $class->name])->assertForbidden();
        $this->assertDatabaseHas('classes', ['id' => $class->id]);
        $this->assertDatabaseHas('class_user', ['class_id' => $class->id, 'user_id' => $student->id]);
    }

    public function test_owner_cannot_remove_someone_not_enrolled_or_delete_without_matching_name(): void
    {
        $teacher = $this->account('guru', 'confirm');
        $student = $this->account('siswa', 'confirm');
        $class = $this->schoolClass($teacher, 'CONFIRM1');

        $this->actingAs($teacher)
            ->delete(route('classroom.students.remove', [$class->id, $student->id]))
            ->assertNotFound();

        $this->actingAs($teacher)
            ->delete(route('classroom.destroy', $class->id), ['confirmation' => 'Kelas Lain'])
            ->assertRedirect()->assertSessionHasErrors('confirmation');
        $this->actingAs($teacher)
            ->delete(route('classroom.destroy', $class->id), [])
            ->assertRedirect()->assertSessionHasErrors('confirmation');
        $this->assertDatabaseHas('classes', ['id' => $class->id]);
    }

    public function test_owner_can_delete_class_and_all_its_learning_data_but_not_student_accounts(): void
    {
        Storage::fake('local');
        $teacher = $this->account('guru', 'delete');
        $student = $this->account('siswa', 'delete');
        $class = $this->schoolClass($teacher, 'DELETE1');
        $other = $this->schoolClass($teacher, 'KEEP111');
        $class->students()->attach($student->id);
        $other->students()->attach($student->id);

        $materialFile = 'mathleaf/materials/deleted-material.pdf';
        $taskFile = 'mathleaf/tasks/deleted-task.pdf';
        Storage::disk('local')->put($materialFile, 'Materi kelas');
        Storage::disk('local')->put($taskFile, 'Jawaban siswa');

        $material = Material::create([
            'class_id' => $class->id, 'author_id' => $teacher->id,
            'title' => 'Materi Lama', 'slug' => 'materi-delete1',
            'type' => 'modul', 'file_path' => $materialFile,
        ]);
        DB::table('material_progress')->insert([
            'user_id' => $student->id, 'material_id' => $material->id,
            'progress_percent' => 100, 'created_at' => now(), 'updated_at' => now(),
        ]);
        $task = Activity::create([
            'class_id' => $class->id, 'title' => 'Tugas Lama',
            'fpbl_stage' => 2, 'mode' => 'tugas',
        ]);
        $submission = Submission::create([
            'activity_id' => $task->id, 'user_id' => $student->id,
            'file_path' => $taskFile, 'score' => 90,
        ]);
        $quiz = Assessment::create([
            'class_id' => $class->id, 'author_id' => $teacher->id,
            'title' => 'Kuis Lama', 'type' => 'kuis', 'is_published' => true,
        ]);
        $question = $quiz->questions()->create([
            'question_text' => '2 + 2 = ...',
            'options' => ['1', '2', '3', '4'],
            'correct_answer' => '3',
        ]);
        $attempt = AssessmentAttempt::create([
            'assessment_id' => $quiz->id, 'user_id' => $student->id,
            'attempt_number' => 1, 'score' => 100, 'status' => 'submitted',
        ]);
        $attempt->answers()->create([
            'question_id' => $question->id, 'answer_text' => '3', 'is_correct' => true,
        ]);
        $thread = ForumThread::create([
            'class_id' => $class->id, 'user_id' => $student->id,
            'title' => 'Diskusi Lama', 'body' => 'Isi diskusi kelas',
        ]);
        $comment = ForumComment::create([
            'forum_thread_id' => $thread->id, 'user_id' => $teacher->id, 'body' => 'Balasan guru',
        ]);

        $this->actingAs($teacher)->withSession(['kelas_aktif_id' => $class->id])
            ->delete(route('classroom.destroy', $class->id), ['confirmation' => $class->name])
            ->assertRedirect(route('classroom.home'))
            ->assertSessionHas('success')
            ->assertSessionMissing('kelas_aktif_id');

        $this->assertDatabaseMissing('classes', ['id' => $class->id]);
        $this->assertDatabaseMissing('materials', ['id' => $material->id]);
        $this->assertDatabaseMissing('material_progress', ['material_id' => $material->id]);
        $this->assertDatabaseMissing('activities', ['id' => $task->id]);
        $this->assertDatabaseMissing('submissions', ['id' => $submission->id]);
        $this->assertDatabaseMissing('assessments', ['id' => $quiz->id]);
        $this->assertDatabaseMissing('questions', ['id' => $question->id]);
        $this->assertDatabaseMissing('assessment_attempts', ['id' => $attempt->id]);
        $this->assertDatabaseMissing('forum_threads', ['id' => $thread->id]);
        $this->assertDatabaseMissing('forum_comments', ['id' => $comment->id]);
        $this->assertDatabaseHas('users', ['id' => $student->id]);
        $this->assertDatabaseHas('classes', ['id' => $other->id]);
        $this->assertDatabaseHas('class_user', ['class_id' => $other->id, 'user_id' => $student->id]);
        Storage::disk('local')->assertMissing($materialFile);
        Storage::disk('local')->assertMissing($taskFile);
    }
}
