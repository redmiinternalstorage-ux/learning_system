<?php

namespace App\Helpers;

use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GoogleSheetHelper
{
    // Apps Script Web App URL
    const WEBHOOK_URL = 'https://script.google.com/macros/s/AKfycbxWjl0cmwOGKMlBH9-r3MqbcyGQhgFp2grlMumf3n1fPUGaxViI6tjWuPkJVzDrvsYx/exec';

    /**
     * Push a single user to Google Sheet.
     * action: 'add' | 'update' | 'delete'
     */
    public static function syncUser(User $user, string $action = 'add'): void
    {
        try {
            Http::timeout(8)->post(self::WEBHOOK_URL, [
                'action'       => $action,
                'id'           => $user->id,
                'name'         => $user->name,
                'email'        => $user->email,
                'role'         => $user->role,
                'status'       => $user->is_blocked ? 'blocked' : 'active',
                'registered_at'=> $user->created_at
                                    ? $user->created_at->format('Y-m-d H:i:s')
                                    : now()->format('Y-m-d H:i:s'),
            ]);
        } catch (\Throwable $e) {
            // Never break app flow — just log
            Log::warning("GoogleSheet sync failed [{$action}] user#{$user->id}: " . $e->getMessage());
        }
    }

    /**
     * Push delete event (only id needed).
     */
    public static function deleteUser(int $userId, string $email): void
    {
        try {
            Http::timeout(8)->post(self::WEBHOOK_URL, [
                'action' => 'delete',
                'id'     => $userId,
                'email'  => $email,
            ]);
        } catch (\Throwable $e) {
            Log::warning("GoogleSheet sync failed [delete] user#{$userId}: " . $e->getMessage());
        }
    }

    /**
     * Sync ALL existing users to Google Sheet (bulk).
     * Called from admin panel "Sync All" button.
     */
    public static function syncAllUsers(): int
    {
        $users = User::orderBy('id')->get();
        $count = 0;
        foreach ($users as $user) {
            try {
                Http::timeout(10)->post(self::WEBHOOK_URL, [
                    'action'       => 'sync',
                    'id'           => $user->id,
                    'name'         => $user->name,
                    'email'        => $user->email,
                    'role'         => $user->role,
                    'status'       => $user->is_blocked ? 'blocked' : 'active',
                    'registered_at'=> $user->created_at
                                        ? $user->created_at->format('Y-m-d H:i:s')
                                        : '',
                ]);
                $count++;
            } catch (\Throwable $e) {
                Log::warning("GoogleSheet bulk sync failed user#{$user->id}: " . $e->getMessage());
            }
        }
        return $count;
    }
}
