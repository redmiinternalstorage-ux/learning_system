<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\SupportTicket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AiChatController extends Controller
{
    /**
     * Process student question and provide instant AI response.
     * Strictly scopes knowledge base to User-side features only (no admin data leaks).
     */
    public function ask(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $user = Auth::user();
        $query = trim(mb_strtolower($request->message));

        // Generate intelligent user-side AI response
        $response = $this->generateAiReply($query, $user);

        return response()->json([
            'ok'            => true,
            'reply'         => $response['reply'],
            'suggest_agent' => $response['suggest_agent'] ?? false,
            'quick_actions' => $response['quick_actions'] ?? [],
        ]);
    }

    private function generateAiReply(string $q, $user): array
    {
        $userName = $user ? $user->name : 'Student';

        // 1. Greet / Intro
        if (preg_match('/\b(hi|hello|hey|namaste|kem cho|greetings|hola)\b/i', $q)) {
            return [
                'reply' => "👋 Hi **{$userName}**! I'm your **Learning Curve AI Assistant**.\n\nI can help you browse courses, track learning progress, submit assignments, view completion & demo certificates, configure date-passwords, or receive your data report sheet on email!",
                'quick_actions' => [
                    ['label' => '🎓 My Progress', 'prompt' => 'How is my course progress?'],
                    ['label' => '📩 Receive Data on Mail', 'action' => 'email_data'],
                    ['label' => '📜 Demo Certificates', 'prompt' => 'Show me demo certificates'],
                    ['label' => '💬 Talk to Live Agent', 'action' => 'connect_agent'],
                ]
            ];
        }

        // 2. Personal Data Sheet / Export / Email
        if (preg_match('/\b(data|download data|receive on mail|mail report|email report|export|pdf report|my data|sheet)\b/i', $q)) {
            return [
                'reply' => "📄 **Personal Data & Learning Sheet**\n\nYou can export your complete personal learning report as a PDF sheet or receive it directly in your registered email inbox ({$user->email}).\n\n• **Download PDF Sheet**: Instant downloadable PDF document.\n• **Receive on Mail**: Sent directly to your email with PDF attachment.",
                'quick_actions' => [
                    ['label' => '📄 Download Data PDF', 'url' => route('profile.data.download')],
                    ['label' => '📩 Receive Data on Mail', 'action' => 'email_data'],
                    ['label' => '⚙️ Open Profile Settings', 'url' => route('profile')],
                ]
            ];
        }

        // 3. Demo Certificates Showcase
        if (preg_match('/\b(demo cert|demo certificate|sample cert|sample certificate|cert demo|demo|dummy cert)\b/i', $q)) {
            return [
                'reply' => "📜 **Demo Certificates Showcase**\n\nWe provide sample demo certificates across multiple domains (Web Dev, Python AI, Cyber Security, UI/UX, Cloud DevOps).\n\nDemo certificates allow you to preview & download sample certificate designs without student names!",
                'quick_actions' => [
                    ['label' => '🚀 View Demo Certificates', 'url' => route('certificates.demo')],
                    ['label' => '📊 My Progress Tracker', 'url' => route('progress.index')],
                ]
            ];
        }

        // 4. Official Course Certificates & Graduation
        if (preg_match('/\b(cert|certificate|degree|pass|100%|download pdf|earn cert|graduate|completion)\b/i', $q)) {
            return [
                'reply' => "🏆 **Official Certificate Guidelines**\n\n1. You earn an official Certificate of Achievement automatically once you reach **100% progress** in an enrolled course.\n2. Course progress increases as your submitted assignments are evaluated & graded by instructors.\n3. View & download completed certificates from **My Courses**.\n4. Explore **Demo Certificates** anytime!",
                'quick_actions' => [
                    ['label' => '📜 View My Courses & Certs', 'url' => route('courses.mine')],
                    ['label' => '✨ Demo Certificates', 'url' => route('certificates.demo')],
                ]
            ];
        }

        // 5. Browse Public Courses Catalog
        if (preg_match('/\b(all courses|available courses|course list|catalog|browse|new courses|subjects|all course)\b/i', $q)) {
            $publicCourses = Course::where('is_published', true)->take(4)->get();
            $reply = "📚 **Available Published Courses**\n\nExplore popular courses available on Learning Curve:\n";
            foreach ($publicCourses as $pc) {
                $reply .= "• **{$pc->title}** (" . ($pc->category ?? 'General') . " - " . ($pc->level ?? 'All Levels') . ")\n";
            }
            $reply .= "\nClick below to browse full course catalog & enroll!";
            return [
                'reply' => $reply,
                'quick_actions' => [
                    ['label' => '🌐 Browse Courses Catalog', 'url' => route('courses.public')],
                    ['label' => '📚 My Enrolled Courses', 'url' => route('courses.mine')],
                ]
            ];
        }

        // 6. Course Progress & Learning Snapshot
        if (preg_match('/\b(progress|my course|my courses|learning|hours|analytics|enrolled|snapshot)\b/i', $q)) {
            if ($user) {
                $courses = $user->courses;
                $count = $courses->count();
                $reply = "📊 **Your Personal Learning Snapshot**\n\nYou are enrolled in **{$count} course(s)**.\n";
                foreach ($courses->take(3) as $c) {
                    $reply .= "• **{$c->title}**: " . ($c->pivot->progress ?? 0) . "% completed\n";
                }
                $reply .= "\nCheck your **Progress Tracker** for interactive charts!";
                return [
                    'reply' => $reply,
                    'quick_actions' => [
                        ['label' => '📈 Progress Tracker', 'url' => route('progress.index')],
                        ['label' => '📚 My Enrolled Courses', 'url' => route('courses.mine')],
                    ]
                ];
            }
        }

        // 7. Lessons, Videos & Learning Content
        if (preg_match('/\b(lesson|lessons|video|videos|lecture|watch|content|module|chapter)\b/i', $q)) {
            return [
                'reply' => "🎥 **Lessons & Course Content**\n\n• Open any enrolled course from **My Courses** to access video lessons & study material.\n• Complete course modules and submit corresponding assignments to boost your course progress score!",
                'quick_actions' => [
                    ['label' => '📚 Go to My Courses', 'url' => route('courses.mine')],
                    ['label' => '📈 Progress Tracker', 'url' => route('progress.index')],
                ]
            ];
        }

        // 8. Assignments, Homework & Submissions
        if (preg_match('/\b(assignment|task|submit|upload|grade|score|marks|homework|due)\b/i', $q)) {
            return [
                'reply' => "📝 **Assignments & Tasks Guide**\n\n• Go to **Assignments** from your left sidebar menu.\n• Submit your task as plain text or upload completed files (PDF/ZIP up to 10MB).\n• Instructor grades your submission and awards points, increasing your course progress!",
                'quick_actions' => [
                    ['label' => '📑 View Assignments', 'url' => route('tasks.index')],
                ]
            ];
        }

        // 9. Support Tickets & Help Center
        if (preg_match('/\b(ticket|helpdesk|complaint|issue|bug|problem|query|help center|support ticket)\b/i', $q)) {
            return [
                'reply' => "🎫 **Help Center & Tickets**\n\n• Need assistance? Raise a support ticket under **Help Center**.\n• Select a category (*Course, Payment, Technical, Account, Assignment*) and describe your issue.\n• Support staff will respond directly on your ticket thread!",
                'quick_actions' => [
                    ['label' => '🎫 Raise Help Ticket', 'url' => route('help.index')],
                    ['label' => '💬 Chat with Live Agent', 'action' => 'connect_agent'],
                ]
            ];
        }

        // 10. Security / Date-Passwords / Account Settings
        if (preg_match('/\b(password|date password|security|otp|forgot|login|reset|profile|avatar|setting)\b/i', $q)) {
            return [
                'reply' => "🔐 **Profile & Security Settings**\n\n• **Date-Password Protection**: Configure day-of-month (1-31) secondary passwords for extra account security.\n• **Profile Update**: Update your full name & account details anytime.\n• **Password Reset**: Use 'Forgot Password' link to reset via email OTP.",
                'quick_actions' => [
                    ['label' => '⚙️ Profile & Security', 'url' => route('profile')],
                    ['label' => '📄 Download Data PDF', 'url' => route('profile.data.download')],
                ]
            ];
        }

        // 11. Announcements & Dashboard Updates
        if (preg_match('/\b(notice|announcement|notification|alert|news|update)\b/i', $q)) {
            return [
                'reply' => "📢 **Announcements & System Alerts**\n\n• Stay updated with official course notices and platform announcements directly on your **Student Dashboard**.",
                'quick_actions' => [
                    ['label' => '🏠 Go to Dashboard', 'url' => route('dashboard')],
                ]
            ];
        }

        // 12. Human Support / Live Agent Chat
        if (preg_match('/\b(agent|human|staff|admin|support|talk|contact|help|person|live chat|chat|whatsapp)\b/i', $q)) {
            return [
                'reply' => "💬 **Live Support Agent**\n\nOur support staff & main admin are ready to assist you live! Click below to connect directly with a staff agent in 1-on-1 live chat.",
                'suggest_agent' => true,
                'quick_actions' => [
                    ['label' => '💬 Connect to Live Agent', 'action' => 'connect_agent'],
                    ['label' => '🎫 Raise Support Ticket', 'url' => route('help.index')],
                ]
            ];
        }

        // 13. System Knowledge Fallback (Student Context Only)
        $activeCourses = Course::where('is_published', true)->count();
        return [
            'reply' => "🤖 I'm trained on all **Learning Curve LMS** student features! We have **{$activeCourses} active courses**, assignments, PDF certificates, demo certificates, date-passwords, live staff chat, and email data exports.\n\nHow can I help you today?",
            'suggest_agent' => true,
            'quick_actions' => [
                ['label' => '📚 Browse Courses', 'url' => route('courses.public')],
                ['label' => '📩 Receive Data on Mail', 'action' => 'email_data'],
                ['label' => '📜 Demo Certificates', 'url' => route('certificates.demo')],
                ['label' => '💬 Chat with Live Agent', 'action' => 'connect_agent'],
            ]
        ];
    }
}
