<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    protected $fillable = ['key', 'value', 'type', 'group', 'label'];

    // Get a setting value by key (with optional default)
    public static function get(string $key, mixed $default = null): mixed
    {
        $s = static::where('key', $key)->first();
        if (!$s) return $default;
        if ($s->type === 'boolean') return (bool) $s->value;
        return $s->value ?? $default;
    }

    // Set or create a setting
    public static function set(string $key, mixed $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
    }

    // Default settings seeded on first visit
    public static function defaults(): array
    {
        return [
            ['key'=>'site_name',          'value'=>'Learning Curve',     'type'=>'text',     'group'=>'general',      'label'=>'Site Name'],
            ['key'=>'site_tagline',        'value'=>'Empowering Learners','type'=>'text',     'group'=>'general',      'label'=>'Site Tagline'],
            ['key'=>'contact_email',       'value'=>'admin@gmail.com',    'type'=>'text',     'group'=>'general',      'label'=>'Contact Email'],
            ['key'=>'maintenance_mode',    'value'=>'0',                  'type'=>'boolean',  'group'=>'general',      'label'=>'Maintenance Mode'],
            ['key'=>'registration_open',   'value'=>'1',                  'type'=>'boolean',  'group'=>'general',      'label'=>'Allow New Registrations'],
            ['key'=>'items_per_page',      'value'=>'15',                 'type'=>'text',     'group'=>'general',      'label'=>'Items Per Page'],
            ['key'=>'max_file_upload_mb',  'value'=>'10',                 'type'=>'text',     'group'=>'general',      'label'=>'Max Upload Size (MB)'],
            ['key'=>'welcome_message',     'value'=>'Welcome back!',      'type'=>'textarea', 'group'=>'messages',     'label'=>'Dashboard Welcome Message'],
            ['key'=>'footer_text',         'value'=>'© 2026 Learning Curve. All rights reserved.', 'type'=>'text', 'group'=>'messages', 'label'=>'Footer Text'],
            ['key'=>'primary_color',       'value'=>'#7c3aed',            'type'=>'color',    'group'=>'appearance',   'label'=>'Primary Color'],
            ['key'=>'otp_expiry_minutes',  'value'=>'5',                  'type'=>'text',     'group'=>'security',     'label'=>'OTP Expiry (Minutes)'],
            ['key'=>'max_login_attempts',  'value'=>'5',                  'type'=>'text',     'group'=>'security',     'label'=>'Max Login Attempts'],
        ];
    }
}
