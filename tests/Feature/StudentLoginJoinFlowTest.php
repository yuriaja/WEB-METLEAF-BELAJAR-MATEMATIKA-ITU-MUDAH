<?php

namespace Tests\Feature;

use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentLoginJoinFlowTest extends TestCase
{
    use RefreshDatabase;

    private function account(string $role, string $email): User
    {
        return User::create([
            'name' => 'Pengguna Uji',
            'email' => $email,
            'password' => 'password123',
            'role' => $role,
        ]);
    }

    private function classroom(User $teacher, string $code): SchoolClass
    {
        return SchoolClass::create([
            'name' => 'VII A Matematika',
            'teacher_id' => $teacher->id,
            'join_code' => $code,
            'max_students' => 30,
        ]);
    }

    public function test_login_page_explains_class_code_is_only_needed_when_first_joining(): void
    {
        $this->get('/masuk')->assertOk()
            ->assertSee('Masuk Sekarang')
            ->assertSee('Kode Kelas Matematika')
            ->assertSee('class_code_visible')
            ->assertSee('maxlength="8"', false)
            ->assertSee('hanya diperlukan saat pertama kali bergabung');
    }

    public function test_student_must_enter_class_code_during_login_and_is_enrolled(): void
    {
        $teacher = $this->account('guru', 'teacher-join@example.test');
        $student = $this->account('siswa', 'student-join@example.test');
        $class = $this->classroom($teacher, 'K1QGBODZ');

        $this->postJson('/api/mathleaf/login', [
            'email' => $student->email, 'password' => 'password123', 'role' => 'siswa',
        ])->assertUnprocessable()->assertJsonValidationErrors('kode_kelas');
        $this->assertGuest();

        $this->postJson('/api/mathleaf/login', [
            'email' => $student->email,
            'password' => 'password123',
            'role' => 'siswa',
            'kode_kelas' => 'k1qgbodz',
        ])->assertOk()->assertJsonPath('redirect', '/dashboard');

        $this->assertAuthenticatedAs($student);
        $this->assertDatabaseHas('class_user', ['class_id' => $class->id, 'user_id' => $student->id]);
        $this->assertSame(1, $student->classes()->count());
    }

    public function test_wrong_class_code_rejects_login_without_authenticating_student(): void
    {
        $teacher = $this->account('guru', 'teacher-wrong@example.test');
        $student = $this->account('siswa', 'student-wrong@example.test');
        $this->classroom($teacher, 'GOOD2026');

        $this->postJson('/api/mathleaf/login', [
            'email' => $student->email,
            'password' => 'password123',
            'role' => 'siswa',
            'kode_kelas' => 'WRONG123',
        ])->assertUnprocessable()->assertJsonValidationErrors('kode_kelas');

        $this->assertGuest();
        $this->assertSame(0, $student->classes()->count());
    }

    public function test_student_cannot_join_second_math_class_by_logging_in_again(): void
    {
        $teacher = $this->account('guru', 'teacher-other@example.test');
        $student = $this->account('siswa', 'student-other@example.test');
        $first = $this->classroom($teacher, 'MATH2026');
        $this->classroom($teacher, 'MATH2027');

        $payload = ['email' => $student->email, 'password' => 'password123', 'role' => 'siswa'];
        $this->postJson('/api/mathleaf/login', $payload + ['kode_kelas' => 'MATH2026'])->assertOk();
        $this->postJson('/api/mathleaf/logout')->assertOk();
        $this->postJson('/api/mathleaf/login', $payload + ['kode_kelas' => 'MATH2027'])
            ->assertUnprocessable()->assertJsonValidationErrors('kode_kelas');

        $this->assertGuest();
        $this->assertSame([$first->id], $student->classes()->pluck('classes.id')->all());
    }

    public function test_teacher_still_logs_in_without_a_class_code(): void
    {
        $teacher = $this->account('guru', 'teacher-login@example.test');

        $this->postJson('/api/mathleaf/login', [
            'email' => $teacher->email,
            'password' => 'password123',
            'role' => 'guru',
        ])->assertOk()->assertJsonPath('redirect', '/guru');

        $this->assertAuthenticatedAs($teacher);
    }
}
