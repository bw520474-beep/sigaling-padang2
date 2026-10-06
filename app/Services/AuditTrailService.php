<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AuditTrailService
{
    /**
     * Catat aktivitas penting ke audit trail
     */
    public static function log(
        string $action,
        string $entityType,
        string $entityId,
        ?array $oldValues = null,
        ?array $newValues = null,
        ?array $actorOverride = null
    ): AuditLog {
        $actorId = null;
        $actorRole = 'system';
        $actorName = 'Sistem Otomatis';

        if ($actorOverride) {
            $actorId = $actorOverride['id'] ?? null;
            $actorRole = $actorOverride['role'] ?? 'system';
            $actorName = $actorOverride['name'] ?? 'Sistem';
        } elseif (Auth::check()) {
            $user = Auth::user();
            $actorId = $user->id;
            $actorRole = $user->role;
            $actorName = $user->nama;
        }

        return AuditLog::create([
            'actor_id' => $actorId,
            'actor_role' => $actorRole,
            'actor_name' => $actorName,
            'action' => $action,
            'entity_type' => $entityType,
            'entity_id' => (string) $entityId,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => Request::ip() ?: '127.0.0.1',
            'user_agent' => substr(Request::userAgent() ?: 'Unknown', 0, 500),
            'created_at' => now(),
        ]);
    }
}
