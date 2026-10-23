<?php

namespace Tests\Feature;

use App\Mail\CourseCertificateMail;
use App\Mail\TicketStatusUpdatedMail;
use App\Models\Course;
use App\Models\SupportTicket;
use App\Models\Task;
use App\Models\TaskSubmission;
use App\Models\User;
use App\Models\UserDatePassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class SystemUpgradeTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_course_progress_updates_and_triggers_certificate()
    {
        Mail::fake();

        $student = User::factory()->create(['role' => 'student']);
        $admin   = User::factory()->create(['role' => 'admin']);

        $course = Course::create([
            'title' => 'Test Laravel Course',
            'description' => 'Test course description',
            'instructor_name' => 'Instructor John',
            'price' => 0,
            'is_published' => true,
        ]);

        $task = Task::create([
            'course_id' => $course->id,
            'title' => 'Task 1',
            'points' => 100,
        ]);

        $student->courses()->attach($course->id, ['progress' => 0]);

        // Student submits task
        $submission = TaskSubmission::create([
            'task_id' => $task->id,
            'user_id' => $student->id,
            'submission_text' => 'Done',
            'status' => 'pending',
        ]);

        // Admin grades task
        $this->actingAs($admin)
            ->post(route('admin.submissions.grade', $submission), [
                'grade' => 95,
                'feedback' => 'Well done!',
            ]);

        // Assert course progress updated to 100%
        $progress = $student->courses()->where('course_id', $course->id)->first()->pivot->progress;
        $this->assertEquals(100, $progress);

        // Assert certificate email was sent
        Mail::assertSent(CourseCertificateMail::class, function ($mail) use ($student) {
            return $mail->hasTo($student->email);
        });
    }

    public function test_date_based_password_authentication()
    {
        $todayDay = (int) now()->format('j');

        $user = User::factory()->create([
            'email' => 'dateuser@example.com',
            'password' => Hash::make('MainPassword123'),
        ]);

        UserDatePassword::create([
            'user_id' => $user->id,
            'day_of_month' => $todayDay,
            'password' => Hash::make('DateSpecialPass123'),
            'is_active' => true,
        ]);

        // Login with date password on matching day
        $response = $this->post('/login', [
            'email' => 'dateuser@example.com',
            'password' => 'DateSpecialPass123',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($user);
    }

    public function test_ticket_status_change_sends_email_notification()
    {
        Mail::fake();

        $student = User::factory()->create(['role' => 'student']);
        $admin   = User::factory()->create(['role' => 'admin']);

        $ticket = SupportTicket::create([
            'user_id' => $student->id,
            'subject' => 'Need Help with Course',
            'message' => 'Please assist',
            'category' => 'course',
            'priority' => 'medium',
            'status' => 'open',
        ]);

        $this->actingAs($admin)
            ->post(route('admin.support.status', $ticket), [
                'status' => 'resolved',
            ]);

        $ticket->refresh();
        $this->assertEquals('resolved', $ticket->status);

        Mail::assertSent(TicketStatusUpdatedMail::class, function ($mail) use ($student) {
            return $mail->hasTo($student->email) && $mail->newStatus === 'resolved';
        });
    }
}
