<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Course;
use App\Models\Task;
use App\Models\TaskSubmission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPanelTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $student;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name'     => 'Admin User',
            'email'    => 'admin@gmail.com',
            'password' => bcrypt('password'),
            'role'     => 'admin',
        ]);

        $this->student = User::create([
            'name'     => 'Student User',
            'email'    => 'student@gmail.com',
            'password' => bcrypt('password'),
            'role'     => 'student',
        ]);
    }

    public function test_non_admin_cannot_access_admin_panel()
    {
        $response = $this->actingAs($this->student)->get('/admin');
        $response->assertRedirect('/dashboard');
    }

    public function test_admin_can_access_admin_dashboard()
    {
        $response = $this->actingAs($this->admin)->get('/admin');
        $response->assertStatus(200);
        $response->assertSee('Welcome back');
    }

    public function test_admin_can_view_users_directory_and_detail_json()
    {
        $response = $this->actingAs($this->admin)->get('/admin/users');
        $response->assertStatus(200);

        $detailResponse = $this->actingAs($this->admin)->get("/admin/users/{$this->student->id}/detail");
        $detailResponse->assertStatus(200);
        $detailResponse->assertJsonPath('user.email', 'student@gmail.com');
    }

    public function test_admin_can_perform_bulk_user_actions()
    {
        $user1 = User::create(['name'=>'U1','email'=>'u1@test.com','password'=>'12345678','role'=>'student']);
        $user2 = User::create(['name'=>'U2','email'=>'u2@test.com','password'=>'12345678','role'=>'student']);

        $response = $this->actingAs($this->admin)->post('/admin/users/bulk', [
            'action'   => 'block',
            'user_ids' => [$user1->id, $user2->id],
        ]);

        $response->assertRedirect();
        $this->assertTrue($user1->fresh()->is_blocked);
        $this->assertTrue($user2->fresh()->is_blocked);
    }

    public function test_admin_can_create_and_bulk_publish_courses()
    {
        $course = Course::create([
            'title'           => 'Test Course',
            'instructor_name' => 'John Instructor',
            'category'        => 'Web Development',
            'level'           => 'Beginner',
            'duration_hours'  => 10,
            'is_published'    => false,
        ]);

        $response = $this->actingAs($this->admin)->post('/admin/courses/bulk', [
            'action'     => 'publish',
            'course_ids' => [$course->id],
        ]);

        $response->assertRedirect();
        $this->assertTrue($course->fresh()->is_published);
    }

    public function test_admin_can_manually_enroll_and_unenroll_student()
    {
        $course = Course::create([
            'title'           => 'Laravel Mastery',
            'instructor_name' => 'Taylor',
            'category'        => 'Web Development',
            'level'           => 'Intermediate',
            'duration_hours'  => 15,
            'is_published'    => true,
        ]);

        $response = $this->actingAs($this->admin)->post('/admin/enrollments', [
            'user_id'   => $this->student->id,
            'course_id' => $course->id,
            'progress'  => 50,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('course_user', [
            'user_id'   => $this->student->id,
            'course_id' => $course->id,
            'progress'  => 50,
        ]);

        $unenrollResponse = $this->actingAs($this->admin)->delete('/admin/enrollments/unenroll', [
            'user_id'   => $this->student->id,
            'course_id' => $course->id,
        ]);

        $unenrollResponse->assertRedirect();
        $this->assertDatabaseMissing('course_user', [
            'user_id'   => $this->student->id,
            'course_id' => $course->id,
        ]);
    }

    public function test_admin_analytics_and_logs_pages_render_without_error()
    {
        $this->actingAs($this->admin)->get('/admin/analytics')->assertStatus(200);
        $this->actingAs($this->admin)->get('/admin/activity-logs')->assertStatus(200);
        $this->actingAs($this->admin)->get('/admin/settings')->assertStatus(200);
        $this->actingAs($this->admin)->get('/admin/mail-logs')->assertStatus(200);
    }
}
