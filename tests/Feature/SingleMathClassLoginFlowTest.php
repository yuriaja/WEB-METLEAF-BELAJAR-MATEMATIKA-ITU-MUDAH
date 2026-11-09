<?php

namespace Tests\Feature;

use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class SingleMathClassLoginFlowTest extends TestCase
{
    use RefreshDatabase;

    private function person(string $role, string $tag): User
    {
        return User::create([
            'name' => ucfirst($role).' '.$tag,
            'email' => $tag.'@example.test',
            'role' => $role,
            'password' => 'password123',
        ]);
    }

    private function mathClass(User $teacher, string $code, int $capacity = 30): SchoolClass
    {
        return SchoolClass::create([
            'name' => 'VII A Matematika',
            'teacher_id' => $teacher->id,
            'join_code' => $code,
            'max_students' => $capacity,
        ]);
    }

    private function loginData(User $student, ?string $code = null): array
    {
        $data = ['email' => $student->email, 'password' => 'password123', 'role' => 'siswa'];
        if ($code !== null) $data['kode_kelas'] = $code;
        return $data;
    }

    public function test_students_must_enter_eight_character_class_code_every_login(): void
    {
        $teacher = $this->person('guru', 'teacher1');
        $student = $this->person('siswa', 'student1');
        $class = $this->mathClass($teacher, 'VIIA2026');

        $this->postJson('/api/mathleaf/login', $this->loginData($student))
            ->assertUnprocessable()->assertJsonValidationErrors('kode_kelas');
        $this->assertGuest();
        $this->postJson('/api/mathleaf/login', $this->loginData($student, 'VIIA20'))
            ->assertUnprocessable()->assertJsonValidationErrors('kode_kelas');

        $this->postJson('/api/mathleaf/login', $this->loginData($student, 'viia2026'))
            ->assertOk()->assertJsonPath('redirect', '/dashboard');
        $this->assertDatabaseHas('class_user', ['class_id' => $class->id, 'user_id' => $student->id]);
        $this->assertSame($class->id, session('kelas_aktif_id'));
        $this->postJson('/api/mathleaf/logout')->assertOk();

        $this->postJson('/api/mathleaf/login', $this->loginData($student))
            ->assertUnprocessable()->assertJsonValidationErrors('kode_kelas');
        $this->postJson('/api/mathleaf/login', $this->loginData($student, 'VIIA2026'))
            ->assertOk();
        $this->assertSame(1, DB::table('class_user')->where('user_id', $student->id)->count());
    }

    public function test_bad_class_code_does_not_leave_student_authenticated_and_can_retry(): void
    {
        $teacher = $this->person('guru', 'teacher2');
        $student = $this->person('siswa', 'student2');
        $this->mathClass($teacher, 'GOOD2026');
        $this->postJson('/api/mathleaf/login', $this->loginData($student, 'BADX2026'))
            ->assertUnprocessable()->assertJsonValidationErrors('kode_kelas');
        $this->assertGuest();
        $this->postJson('/api/mathleaf/login', $this->loginData($student, 'GOOD2026'))
            ->assertOk();
    }

    public function test_student_can_only_belong_to_one_math_class(): void
    {
        $teacher = $this->person('guru', 'teacher3');
        $classOne = $this->mathClass($teacher, 'MATH2026');
        $classTwo = $this->mathClass($teacher, 'MATH2027');
        $student = $this->person('siswa', 'student3');
        $this->postJson('/api/mathleaf/login', $this->loginData($student, 'MATH2026'))->assertOk();
        $this->postJson('/api/mathleaf/logout')->assertOk();
        $this->postJson('/api/mathleaf/login', $this->loginData($student, 'MATH2027'))
            ->assertUnprocessable()->assertJsonValidationErrors('kode_kelas');
        $this->assertDatabaseHas('class_user', ['class_id' => $classOne->id, 'user_id' => $student->id]);
        $this->assertDatabaseMissing('class_user', ['class_id' => $classTwo->id, 'user_id' => $student->id]);
        $this->assertSame(1, DB::table('class_user')->where('user_id', $student->id)->count());
    }

    public function test_teacher_controls_capacity_and_full_class_rejects_new_members_only(): void
    {
        $teacher = $this->person('guru', 'teacher4');
        $student = $this->person('siswa', 'student4');
        $newcomer = $this->person('siswa', 'student5');
        $class = $this->mathClass($teacher, 'FULL2026', 1);
        $this->postJson('/api/mathleaf/login', $this->loginData($student, 'FULL2026'))->assertOk();
        $this->postJson('/api/mathleaf/logout')->assertOk();
        $this->postJson('/api/mathleaf/login', $this->loginData($newcomer, 'FULL2026'))
            ->assertUnprocessable()->assertJsonValidationErrors('kode_kelas');
        $this->postJson('/api/mathleaf/login', $this->loginData($student, 'FULL2026'))->assertOk();
        $this->postJson('/api/mathleaf/logout')->assertOk();

        $this->actingAs($teacher)->patch(route('classroom.capacity.update', $class), ['max_students' => 2])
            ->assertRedirect()->assertSessionHas('success');
        $this->assertDatabaseHas('classes', ['id' => $class->id, 'max_students' => 2]);
        $this->patch(route('classroom.capacity.update', $class), ['max_students' => 0])
            ->assertSessionHasErrors('max_students');
        $this->patch(route('classroom.capacity.update', $class), ['max_students' => 1])
            ->assertRedirect();
        $otherStudent = $this->person('siswa', 'newcomer');
        $this->actingAs($otherStudent)->patch(route('classroom.capacity.update', $class), ['max_students' => 200])
            ->assertForbidden();
        $this->assertDatabaseHas('classes', ['id' => $class->id, 'max_students' => 1]);
    }

    public function test_teacher_permission_allows_kicked_student_to_rejoin_on_login(): void
    {
        $teacher = $this->person('guru', 'teacher5');
        $student = $this->person('siswa', 'student6');
        $class = $this->mathClass($teacher, 'KICK2026');
        $this->postJson('/api/mathleaf/login', $this->loginData($student, 'KICK2026'))->assertOk();
        $this->postJson('/api/mathleaf/logout')->assertOk();
        $this->actingAs($teacher)->delete(route('classroom.students.remove', [$class, $student]))
            ->assertRedirect();
        $this->assertDatabaseHas('class_kicked_users', ['class_id' => $class->id, 'user_id' => $student->id]);
        $this->postJson('/api/mathleaf/login', $this->loginData($student, 'KICK2026'))
            ->assertUnprocessable()->assertJsonValidationErrors('kode_kelas');
        $this->actingAs($teacher)->patch(route('classroom.students.allow', [$class, $student]))->assertRedirect();
        $this->assertDatabaseMissing('class_kicked_users', ['class_id' => $class->id, 'user_id' => $student->id]);
        $this->postJson('/api/mathleaf/login', $this->loginData($student, 'KICK2026'))->assertOk();
    }

    public function test_student_registration_redirects_to_login_and_teacher_needs_no_code(): void
    {
        $response = $this->postJson('/api/mathleaf/register', [
            'name' => 'Siswa Baru',
            'email' => 'new.student@example.test',
            'password' => 'password123',
            'role' => 'siswa',
        ]);
        $response->assertCreated()->assertJsonPath('redirect', '/masuk?registered=1');
        $this->assertGuest();

        $this->postJson('/api/mathleaf/register', [
            'name' => 'Guru Baru',
            'email' => 'new.teacher@example.test',
            'password' => 'password123',
            'role' => 'guru',
        ])->assertCreated()->assertJsonPath('redirect', '/guru');
        $this->postJson('/api/mathleaf/logout')->assertOk();
        $teacher = User::where('email', 'new.teacher@example.test')->firstOrFail();
        $this->postJson('/api/mathleaf/login', [
            'email' => $teacher->email, 'password' => 'password123', 'role' => 'guru',
        ])->assertOk()->assertJsonPath('redirect', '/guru');
    }

    public function test_teacher_sees_polished_confirm_modals_and_capacity_form(): void
    {
        $teacher = $this->person('guru', 'teacher7');
        $student = $this->person('siswa', 'student7');
        $class = $this->mathClass($teacher, 'LOOK2026', 30);
        $class->students()->attach($student->id);
        $this->actingAs($teacher)->get(route('classroom.home'))
            ->assertOk()
            ->assertSee('action-dialog')
            ->assertSee('Batas siswa')
            ->assertSee('30')
            ->assertSee('Keluarkan Siswa?');
        $this->get(route('login'))->assertOk()->assertSee('class_code_visible');
        $this->get(route('register'))->assertOk()->assertDontSee('name="kode_kelas"', false);
    }
}
