<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Collection;
use Spatie\Activitylog\Models\Activity;

/**
 * Read-only access to the audit log.
 */
class ActivityLogService
{
    /**
     * @param  array{log_name?: string|null, event?: string|null, limit?: int|string|null}  $filters
     * @return Collection<int, Activity>
     */
    public function latest(array $filters = []): Collection
    {
        $limit = min(max((int) ($filters['limit'] ?? 50), 1), 200);

        return Activity::query()
            ->when($filters['log_name'] ?? null, fn ($query, $logName) => $query->where('log_name', $logName))
            ->when($filters['event'] ?? null, fn ($query, $event) => $query->where('event', $event))
            ->latest()
            ->orderByDesc('id')
            ->limit($limit)
            ->get();
    }
}
