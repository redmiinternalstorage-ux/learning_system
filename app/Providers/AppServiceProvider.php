<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Cache invalidation hooks on CUD events
        \App\Models\User::saved(fn() => \Illuminate\Support\Facades\Cache::forget('admin_dashboard_stats'));
        \App\Models\User::deleted(fn() => \Illuminate\Support\Facades\Cache::forget('admin_dashboard_stats'));
        
        \App\Models\Course::saved(function() {
            \Illuminate\Support\Facades\Cache::forget('admin_dashboard_stats');
            \Illuminate\Support\Facades\Cache::forget('home_featured_courses');
            \Illuminate\Support\Facades\Cache::forget('home_total_courses_count');
            \Illuminate\Support\Facades\Cache::forget('published_categories');
        });
        \App\Models\Course::deleted(function() {
            \Illuminate\Support\Facades\Cache::forget('admin_dashboard_stats');
            \Illuminate\Support\Facades\Cache::forget('home_featured_courses');
            \Illuminate\Support\Facades\Cache::forget('home_total_courses_count');
            \Illuminate\Support\Facades\Cache::forget('published_categories');
        });

        \App\Models\Announcement::saved(fn() => \Illuminate\Support\Facades\Cache::forget('active_announcements_top5'));
        \App\Models\Announcement::deleted(fn() => \Illuminate\Support\Facades\Cache::forget('active_announcements_top5'));

        \App\Models\SupportTicket::saved(fn() => \Illuminate\Support\Facades\Cache::forget('sidebar_unread_tickets'));
        \App\Models\SupportTicket::deleted(fn() => \Illuminate\Support\Facades\Cache::forget('sidebar_unread_tickets'));
        \App\Models\ChatMessage::saved(fn() => \Illuminate\Support\Facades\Cache::forget('sidebar_waiting_chats'));
        \App\Models\ChatMessage::deleted(fn() => \Illuminate\Support\Facades\Cache::forget('sidebar_waiting_chats'));
        \App\Models\User::saved(function() {
            \Illuminate\Support\Facades\Cache::forget('sidebar_user_count');
            \Illuminate\Support\Facades\Cache::forget('sidebar_staff_count');
        });
        \App\Models\User::deleted(function() {
            \Illuminate\Support\Facades\Cache::forget('sidebar_user_count');
            \Illuminate\Support\Facades\Cache::forget('sidebar_staff_count');
        });
        \App\Models\Course::saved(fn() => \Illuminate\Support\Facades\Cache::forget('sidebar_course_count'));
        \App\Models\Course::deleted(fn() => \Illuminate\Support\Facades\Cache::forget('sidebar_course_count'));
        \App\Models\TaskSubmission::saved(fn() => \Illuminate\Support\Facades\Cache::forget('sidebar_pending_count'));
        \App\Models\TaskSubmission::deleted(fn() => \Illuminate\Support\Facades\Cache::forget('sidebar_pending_count'));

        \App\Models\Ad::saved(fn() => \Illuminate\Support\Facades\Cache::forget('active_ad_overlay'));
        \App\Models\Ad::deleted(fn() => \Illuminate\Support\Facades\Cache::forget('active_ad_overlay'));
    }
}
