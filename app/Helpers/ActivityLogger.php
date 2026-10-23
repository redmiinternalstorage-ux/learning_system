<?php

namespace App\Helpers;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class ActivityLogger
{
    public static function log(
        string  $action,
        ?string $targetType  = null,
        ?int    $targetId    = null,
        ?string $targetLabel = null,
        ?string $description = null,
        ?int    $userId      = null,
    ): ActivityLog {
        return ActivityLog::create([
            'user_id'      => $userId ?? (Auth::id() ?? null),
            'action'       => $action,
            'target_type'  => $targetType,
            'target_id'    => $targetId,
            'target_label' => $targetLabel,
            'description'  => $description,
            'ip'           => Request::ip(),
        ]);
    }
}
