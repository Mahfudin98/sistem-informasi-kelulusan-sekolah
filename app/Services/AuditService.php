<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\AuditRepository;
use App\Core\Session;

/**
 * Audit Service — Records user actions.
 */
final class AuditService
{
    private static ?AuditRepository $repo = null;

    private static function getRepo(): AuditRepository
    {
        if (self::$repo === null) {
            self::$repo = new AuditRepository();
        }
        return self::$repo;
    }

    /**
     * Record an action.
     */
    public static function log(
        string $action,
        string $entity,
        ?int $entityId = null,
        string $description = '',
        ?array $oldValues = null,
        ?array $newValues = null
    ): void {
        try {
            $user = Session::get('user');
            
            self::getRepo()->create([
                'user_id'     => isset($user['id']) ? (int) $user['id'] : null,
                'username'    => $user['username'] ?? 'guest',
                'action'      => $action,
                'entity'      => $entity,
                'entity_id'   => $entityId,
                'description' => $description,
                'old_values'  => $oldValues ? json_encode($oldValues) : null,
                'new_values'  => $newValues ? json_encode($newValues) : null,
                'ip_address'  => $_SERVER['REMOTE_ADDR'] ?? null,
                'user_agent'  => $_SERVER['HTTP_USER_AGENT'] ?? null,
            ]);
        } catch (\Exception $e) {
            // Silently fail logging to prevent breaking main flow
            error_log("Audit log failed: " . $e->getMessage());
        }
    }

    /**
     * Get paginated logs for display.
     */
    public function getPaginatedLogs(int $page = 1, int $perPage = 25): array
    {
        return self::getRepo()->paginate($page, $perPage);
    }
}
